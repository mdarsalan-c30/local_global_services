import zipfile
import xml.etree.ElementTree as ET
import os

docx_path = r"d:\websiteproject\SAPIT WEBSITE FINAL\resources\Images.docx"

if not os.path.exists(docx_path):
    print("Docx file not found!")
    exit(1)

with zipfile.ZipFile(docx_path, 'r') as zip_ref:
    file_list = zip_ref.namelist()
    print("Files in docx archive (first 30):")
    for f in file_list[:30]:
        print(f" - {f}")
    
    media_files = [f for f in file_list if f.startswith('word/media/')]
    print(f"\nTotal media files: {len(media_files)}")
    for m in media_files:
        print(f" - {m}")

    # Let's try to extract text from word/document.xml
    try:
        doc_xml = zip_ref.read('word/document.xml')
        root = ET.fromstring(doc_xml)
        
        # Word XML namespaces
        namespaces = {
            'w': 'http://schemas.openxmlformats.org/wordprocessingml/2006/main'
        }
        
        # Extract all text paragraphs
        paragraphs = []
        for p in root.findall('.//w:p', namespaces):
            texts = []
            for t in p.findall('.//w:t', namespaces):
                if t.text:
                    texts.append(t.text)
            if texts:
                paragraphs.append("".join(texts))
                
        print("\nDocument Text Paragraphs (first 100 lines):")
        for i, para in enumerate(paragraphs[:100]):
            print(f"{i+1}: {para}")
            
    except Exception as e:
        print(f"Error extracting text: {e}")
