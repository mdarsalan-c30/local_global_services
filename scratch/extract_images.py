import zipfile
import xml.etree.ElementTree as ET
import os
import sqlite3

docx_path = r"d:\websiteproject\SAPIT WEBSITE FINAL\resources\Images.docx"
output_dir = r"d:\websiteproject\SAPIT WEBSITE FINAL\resources\uploads"
db_path = r"d:\websiteproject\SAPIT WEBSITE FINAL\api\database.db"

if not os.path.exists(output_dir):
    os.makedirs(output_dir)

# Mapping from document text (cleaned) to database service key
doc_to_key_map = {
    'sap analytics': 'sap-analytics-cloud',
    'sap hcm implementation': 'sap-hcm',
    'continuous application management': 'continuous-ams',
    'cloud transformation & migration & upgrade': 'sap-cloud', # we will map to sap-cloud
    'ecc to s/4hana conversion': 'sap-s4hana', # S4HANA or migration? Let's check
    'sap ariba': 'sap-ariba',
    's4 hana cloud': 's4-hana-cloud',
    'production track': 'iot-production',
    'vehicle live tracking system': 'iot-vehicle',
    'rise with sap': 'rise-sap',
    'grow with sap': 'grow-sap',
    'business technology platform': 'sap-btp',
    'fiori enablement': 'fiori-enablement',
    'power bi': 'power-bi',
    'refx implementation': 'sap-refx',
    'dms': 'sap-dms',
    'environment health & safety management': 'sap-ehs',
    'open text': 'sap-opentext',
    'resource augmentation': 'resource-augmentation',
    'fuel consumption monitoring system': 'iot-fuel',
    'automotive': 'ind-automotive',
    'life sciences & pharma tech': 'ind-lifesciences',
    'engineer. procure. construct. digitally.': 'ind-epc',
    'energy & utility services': 'ind-energy',
    'sap for manufacturing excellence': 'ind-manufacturing',
    'real estate': 'ind-realestate',
    'retail transformation services': 'ind-retail'
}

with zipfile.ZipFile(docx_path, 'r') as zip_ref:
    # Get relationship mapping
    rels_xml = zip_ref.read('word/_rels/document.xml.rels')
    rels_root = ET.fromstring(rels_xml)
    rel_ns = {'rel': 'http://schemas.openxmlformats.org/package/2006/relationships'}
    rid_map = {}
    for rel in rels_root.findall('.//rel:Relationship', rel_ns):
        rId = rel.get('Id')
        target = rel.get('Target')
        rid_map[rId] = target

    # Parse main document sequentially
    doc_xml = zip_ref.read('word/document.xml')
    doc_root = ET.fromstring(doc_xml)
    w_ns = {
        'w': 'http://schemas.openxmlformats.org/wordprocessingml/2006/main',
        'r': 'http://schemas.openxmlformats.org/officeDocument/2006/relationships',
        'a': 'http://schemas.openxmlformats.org/drawingml/2006/main',
        'pic': 'http://schemas.openxmlformats.org/drawingml/2006/picture',
        'v': 'urn:schemas-microsoft-com:vml'
    }

    body = doc_root.find('.//w:body', w_ns)
    if body is None:
        print("No body found!")
        exit(1)
        
    # We will traverse sequentially to find text followed by images
    current_title = None
    extracted_images = {} # title -> list of images
    
    for child in body:
        if child.tag.endswith('p'):
            # Text extraction
            texts = []
            for t in child.findall('.//w:t', w_ns):
                if t.text:
                    texts.append(t.text)
            text_str = "".join(texts).strip()
            
            if text_str:
                # If this text is one of our target titles
                cleaned_text = text_str.lower().strip()
                # Check for substring match in keys
                matched_key = None
                for k in doc_to_key_map.keys():
                    if k in cleaned_text or cleaned_text in k:
                        matched_key = k
                        break
                
                if matched_key:
                    current_title = matched_key
                    if current_title not in extracted_images:
                        extracted_images[current_title] = []
            
            # Find drawings/images
            images = []
            for blip in child.findall('.//a:blip', w_ns):
                embed_id = blip.get('{http://schemas.openxmlformats.org/officeDocument/2006/relationships}embed')
                if embed_id and embed_id in rid_map:
                    images.append(rid_map[embed_id])
            for img_data in child.findall('.//v:imagedata', w_ns):
                rel_id = img_data.get('{http://schemas.openxmlformats.org/officeDocument/2006/relationships}id')
                if rel_id and rel_id in rid_map:
                    images.append(rid_map[rel_id])
                    
            if current_title and images:
                extracted_images[current_title].extend(images)

    # Now let's extract files, rename them and prepare DB updates
    db_updates = []
    print("\nExtraction & Mapping Results:")
    for title, imgs in extracted_images.items():
        if not imgs:
            print(f" - Title '{title}': No images found.")
            continue
            
        # Get target key
        service_key = doc_to_key_map[title]
        # We will extract the first image found for this title
        img_in_zip = imgs[0]
        # Adjust path (word/media/imageX.png)
        zip_img_path = f"word/{img_in_zip}" if not img_in_zip.startswith('word/') else img_in_zip
        
        # Extension
        ext = os.path.splitext(zip_img_path)[1]
        dest_filename = f"docx_{service_key}{ext}"
        dest_filepath = os.path.join(output_dir, dest_filename)
        
        try:
            # Read from zip and write to file
            img_data = zip_ref.read(zip_img_path)
            with open(dest_filepath, 'wb') as f:
                f.write(img_data)
                
            db_relative_url = f"resources/uploads/{dest_filename}"
            db_updates.append((db_relative_url, service_key))
            print(f" - Title '{title}' -> Key '{service_key}' -> Saved image: {db_relative_url} (source: {zip_img_path})")
        except Exception as e:
            print(f" - Title '{title}': Error extracting {zip_img_path}: {e}")

    # Let's perform updates on SQLite database
    if db_updates and os.path.exists(db_path):
        conn = sqlite3.connect(db_path)
        cursor = conn.cursor()
        
        updated_count = 0
        for img_url, key in db_updates:
            cursor.execute("UPDATE submenus SET image_url = ? WHERE service_key = ?", (img_url, key))
            if cursor.rowcount > 0:
                updated_count += cursor.rowcount
                print(f"Database updated: '{key}' -> '{img_url}'")
            else:
                print(f"Database warning: Key '{key}' not found or no change.")
                
        conn.commit()
        conn.close()
        print(f"\nSuccessfully updated {updated_count} rows in SQLite database.")
