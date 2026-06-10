import zipfile
import xml.etree.ElementTree as ET
import os

docx_path = r"d:\websiteproject\SAPIT WEBSITE FINAL\resources\Images.docx"

with zipfile.ZipFile(docx_path, 'r') as zip_ref:
    # 1. Parse relationship file to map rId -> target filename
    rels_xml = zip_ref.read('word/_rels/document.xml.rels')
    rels_root = ET.fromstring(rels_xml)
    
    # Namespaces for rels
    rel_ns = {'rel': 'http://schemas.openxmlformats.org/package/2006/relationships'}
    rid_map = {}
    for rel in rels_root.findall('.//rel:Relationship', rel_ns):
        rId = rel.get('Id')
        target = rel.get('Target')
        rid_map[rId] = target
        
    print(f"Mapped {len(rid_map)} relationships.")

    # 2. Parse main document XML sequentially
    doc_xml = zip_ref.read('word/document.xml')
    doc_root = ET.fromstring(doc_xml)
    
    # Word namespaces
    w_ns = {
        'w': 'http://schemas.openxmlformats.org/wordprocessingml/2006/main',
        'r': 'http://schemas.openxmlformats.org/officeDocument/2006/relationships',
        'a': 'http://schemas.openxmlformats.org/drawingml/2006/main',
        'pic': 'http://schemas.openxmlformats.org/drawingml/2006/picture',
        'v': 'urn:schemas-microsoft-com:vml'
    }

    # We will traverse paragraph elements in document body
    body = doc_root.find('.//w:body', w_ns)
    if body is None:
        print("No body found!")
        exit(1)
        
    sequence = []
    
    for child in body:
        # Check if paragraph w:p
        if child.tag.endswith('p'):
            # Extract text
            texts = []
            for t in child.findall('.//w:t', w_ns):
                if t.text:
                    texts.append(t.text)
            text_str = "".join(texts).strip()
            
            # Find drawings (images) inside this paragraph
            images = []
            # Find blip elements which point to relationships
            for blip in child.findall('.//a:blip', w_ns):
                embed_id = blip.get('{http://schemas.openxmlformats.org/officeDocument/2006/relationships}embed')
                if embed_id and embed_id in rid_map:
                    images.append(rid_map[embed_id])
                    
            # Find VML imagedata elements
            for img_data in child.findall('.//v:imagedata', w_ns):
                rel_id = img_data.get('{http://schemas.openxmlformats.org/officeDocument/2006/relationships}id')
                if rel_id and rel_id in rid_map:
                    images.append(rid_map[rel_id])
            
            if text_str or images:
                sequence.append({
                    'text': text_str,
                    'images': images
                })
        
        # Check if table w:tbl
        elif child.tag.endswith('tbl'):
            # Try to see if there is text or images inside cells
            for row in child.findall('.//w:tr', w_ns):
                for cell in row.findall('.//w:tc', w_ns):
                    cell_texts = []
                    for t in cell.findall('.//w:t', w_ns):
                        if t.text:
                            cell_texts.append(t.text)
                    cell_text_str = "".join(cell_texts).strip()
                    
                    cell_images = []
                    for blip in cell.findall('.//a:blip', w_ns):
                        embed_id = blip.get('{http://schemas.openxmlformats.org/officeDocument/2006/relationships}embed')
                        if embed_id and embed_id in rid_map:
                            cell_images.append(rid_map[embed_id])
                    for img_data in cell.findall('.//v:imagedata', w_ns):
                        rel_id = img_data.get('{http://schemas.openxmlformats.org/officeDocument/2006/relationships}id')
                        if rel_id and rel_id in rid_map:
                            cell_images.append(rid_map[rel_id])
                            
                    if cell_text_str or cell_images:
                        sequence.append({
                            'text': f"[TABLE CELL] {cell_text_str}",
                            'images': cell_images
                        })

    print("\nParsed Sequence:")
    for idx, item in enumerate(sequence):
        img_info = f", Images: {item['images']}" if item['images'] else ""
        text_info = f"Text: '{item['text']}'" if item['text'] else "Image only paragraph"
        print(f"{idx+1}: {text_info}{img_info}")
