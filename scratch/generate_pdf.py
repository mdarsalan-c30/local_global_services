import sys
import subprocess

try:
    from fpdf import FPDF
except ImportError:
    print("fpdf2 not found. Installing...")
    subprocess.check_call([sys.executable, "-m", "pip", "install", "fpdf2"])
    from fpdf import FPDF

class PDFReport(FPDF):
    def header(self):
        # Title heading
        self.set_font('helvetica', 'B', 14)
        self.set_text_color(11, 53, 109) # LGS Navy
        self.cell(0, 10, 'Noida Market Development Cost Estimation Report', 0, 1, 'C')
        
        # Red separator line
        self.set_draw_color(227, 27, 35) # LGS Red
        self.set_line_width(0.8)
        self.line(10, 20, 200, 20)
        self.ln(8)

    def footer(self):
        # Position at 1.5 cm from bottom
        self.set_y(-15)
        self.set_font('helvetica', 'I', 8)
        self.set_text_color(128, 128, 128)
        self.cell(0, 10, f'Page {self.page_no()}', 0, 0, 'C')

def build_pdf():
    pdf = PDFReport()
    pdf.add_page()
    pdf.set_font("helvetica", size=10)
    pdf.set_text_color(51, 51, 51)
    
    # Metadata fields
    pdf.set_font("helvetica", 'B', 10)
    pdf.cell(50, 6, "Project Name:", 0, 0)
    pdf.set_font("helvetica", size=10)
    pdf.cell(0, 6, "Local Global Services (LGS) Dynamic CMS Website", 0, 1)
    
    pdf.set_font("helvetica", 'B', 10)
    pdf.cell(50, 6, "Location Hub:", 0, 0)
    pdf.set_font("helvetica", size=10)
    pdf.cell(0, 6, "Noida (NCR), India", 0, 1)
    
    pdf.set_font("helvetica", 'B', 10)
    pdf.cell(50, 6, "Estimated Market Value:", 0, 0)
    pdf.set_font("helvetica", 'B', 10)
    pdf.set_text_color(227, 27, 35) # Red
    pdf.cell(0, 6, "INR 1,95,000 - INR 3,00,000", 0, 1)
    pdf.set_text_color(51, 51, 51)
    pdf.ln(8)
    
    # Section 1
    pdf.set_font("helvetica", 'B', 12)
    pdf.set_text_color(11, 53, 109)
    pdf.cell(0, 8, "1. Project Overview & Deliverables", 0, 1)
    pdf.set_font("helvetica", size=9.5)
    pdf.set_text_color(51, 51, 51)
    overview_text = (
        "This cost estimation is modeled on the professional dynamic PHP + SQLite web application "
        "developed for the LGS platform. The website bridges standard informational layouts with a "
        "custom database-driven content management system (CMS) and an administrative CRUD dashboard.\n\n"
        "Core Deliverables:\n"
        "  - Interactive Frontend UI/UX: Premium navy/red design system, responsive grids, and animations.\n"
        "  - Dynamic Navbar & Sitemap: Mega-menus mapped directly to database categories and submenus.\n"
        "  - Multi-Language Engine: 5-Language dictionary-based translation system.\n"
        "  - Site Settings CMS: Dynamic site-wide contact info (phone/email) updated dynamically via REST API.\n"
        "  - News & Resources (Blog CMS): Grid listing, search box, reader view, and dynamic SEO updates.\n"
        "  - Secure Administrator Control Room: Custom styled admin login and tabs-driven CRUD dashboard.\n"
        "  - Rich Text Visual Editor: Summernote WYSIWYG editor integration.\n"
        "  - Cloudinary Upload APIs: Cloudinary image upload integration with local directory fallback."
    )
    pdf.multi_cell(0, 5, overview_text)
    pdf.ln(6)
    
    # Section 2
    pdf.set_font("helvetica", 'B', 12)
    pdf.set_text_color(11, 53, 109)
    pdf.cell(0, 8, "2. Component-Wise Development Valuation (INR)", 0, 1)
    pdf.ln(1)
    
    # Table Header
    pdf.set_font("helvetica", 'B', 9)
    pdf.set_fill_color(233, 236, 239)
    pdf.cell(50, 7, "  Development Module", 1, 0, 'L', True)
    pdf.cell(95, 7, "  Scope & Complexity", 1, 0, 'L', True)
    pdf.cell(45, 7, "  Noida Rate (INR)", 1, 1, 'L', True)
    
    # Table Rows
    pdf.set_font("helvetica", size=8.5)
    rows = [
        ("Front-End Design & UI/UX", "Custom CSS, responsive grid, animations, maps", "INR 40,000 - 60,000"),
        ("Multi-Language Engine", "5-Language dictionary selector & dynamic replacement", "INR 15,000 - 25,000"),
        ("Backend & SQLite DB", "SQLite, init.php db setup, dynamic REST APIs", "INR 45,000 - 70,000"),
        ("Secure Admin Panel & CMS", "Secure auth, Lead tracking, menu & content CRUD", "INR 50,000 - 75,000"),
        ("Blog System & APIs", "Summernote, Cloudinary API, dynamic SEO tags", "INR 30,000 - 45,000"),
        ("Testing, Setup & Launch", "Security checks, syntax validations, deployment", "INR 15,000 - 25,000")
    ]
    
    for r in rows:
        pdf.cell(50, 7, f"  {r[0]}", 1, 0)
        pdf.cell(95, 7, f"  {r[1]}", 1, 0)
        pdf.cell(45, 7, f"  {r[2]}", 1, 1)
        
    pdf.set_font("helvetica", 'B', 9)
    pdf.cell(145, 7, "  TOTAL ESTIMATED VALUE", 1, 0, 'R', True)
    pdf.cell(45, 7, "  INR 1,95,000 - 3,00,000", 1, 1, 'L', True)
    pdf.ln(6)
    
    # Section 3
    pdf.set_font("helvetica", 'B', 12)
    pdf.set_text_color(11, 53, 109)
    pdf.cell(0, 8, "3. Market Vendor Pricing Tiers in Noida", 0, 1)
    pdf.set_font("helvetica", size=9.5)
    pdf.set_text_color(51, 51, 51)
    
    tiers_text = (
        "Tier 1: Independent Developers / Freelancers\n"
        "  - Cost Range: INR 60,000 - INR 95,000\n"
        "  - Characteristics: Budget-friendly, but lacks full QA testing, SEO audits, or security patches.\n\n"
        "Tier 2: Noida Boutique IT Agencies (Project Standard)\n"
        "  - Cost Range: INR 1,50,000 - INR 2,80,000\n"
        "  - Characteristics: High visual fidelity, professional database architecture, secure admin dashboards, dedicated project manager, and comprehensive testing.\n\n"
        "Tier 3: Enterprise-Tier Development Firms\n"
        "  - Cost Range: INR 3,50,000 - INR 5,00,000+\n"
        "  - Characteristics: Formal corporate SLAs, rigorous code auditing, and dedicated post-launch support desks."
    )
    pdf.multi_cell(0, 5, tiers_text)
    
    # Output PDF file
    pdf.output("noida_development_cost_estimation.pdf")
    print("PDF generated successfully.")

if __name__ == '__main__':
    build_pdf()
