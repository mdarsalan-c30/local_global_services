<?php
/**
 * Database Initializer & Setup Script (CMS Edition)
 * Sets up SQLite database and pre-populates all menus and dynamic services content.
 */

require_once __DIR__ . '/db_connect.php';

try {
    $db = getDatabaseConnection();
    
    // SQL Dialect Helper
    $isMySQL = (DB_MODE === 'mysql');
    $pkType = $isMySQL ? "id INT AUTO_INCREMENT PRIMARY KEY" : "id INTEGER PRIMARY KEY AUTOINCREMENT";
    $datetimeType = $isMySQL ? "TIMESTAMP DEFAULT CURRENT_TIMESTAMP" : "DATETIME DEFAULT CURRENT_TIMESTAMP";
    $insertIgnore = $isMySQL ? "INSERT IGNORE" : "INSERT OR IGNORE";
    
    // Create Submissions Table (Leads)
    $db->exec("CREATE TABLE IF NOT EXISTS submissions (
        $pkType,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(50) DEFAULT NULL,
        service VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        created_at $datetimeType,
        status VARCHAR(50) DEFAULT 'Pending'
    )");
    
    // Create Admins Table
    $db->exec("CREATE TABLE IF NOT EXISTS admins (
        $pkType,
        username VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL
    )");
    
    // Create Menus Table (Navbar Parent Categories)
    $db->exec("CREATE TABLE IF NOT EXISTS menus (
        $pkType,
        name VARCHAR(255) NOT NULL,
        sort_order INTEGER DEFAULT 0
    )");
    
    // Create Submenus Table (Sub-items and Dynamic Services Page content)
    $db->exec("CREATE TABLE IF NOT EXISTS submenus (
        $pkType,
        menu_id INTEGER NOT NULL,
        name VARCHAR(255) NOT NULL,
        service_key VARCHAR(100) UNIQUE NOT NULL,
        tagline VARCHAR(255) NOT NULL,
        icon VARCHAR(100) NOT NULL,
        desc1 TEXT NOT NULL,
        desc2 TEXT NOT NULL,
        features TEXT NOT NULL,
        banner_grad VARCHAR(255) NOT NULL,
        sort_order INTEGER DEFAULT 0,
        image_url VARCHAR(255) DEFAULT NULL,
        FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE CASCADE
    )");
    
    // Create Settings Table
    $db->exec("CREATE TABLE IF NOT EXISTS settings (
        $pkType,
        `key` VARCHAR(100) UNIQUE NOT NULL,
        value TEXT NOT NULL
    )");
    
    // Create Blogs Table
    $db->exec("CREATE TABLE IF NOT EXISTS blogs (
        $pkType,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(100) UNIQUE NOT NULL,
        summary TEXT NOT NULL,
        content TEXT NOT NULL,
        seo_title VARCHAR(255) NOT NULL,
        meta_description TEXT NOT NULL,
        image_url VARCHAR(255) DEFAULT NULL,
        created_at $datetimeType,
        author VARCHAR(100) DEFAULT NULL
    )");
    
    // Create Testimonials Table
    $db->exec("CREATE TABLE IF NOT EXISTS testimonials (
        $pkType,
        client_name VARCHAR(255) NOT NULL,
        service_name VARCHAR(255) NOT NULL,
        testimonial_text TEXT NOT NULL,
        image_url VARCHAR(255) DEFAULT NULL,
        sort_order INTEGER DEFAULT 0,
        created_at $datetimeType
    )");
    
    // Create Industries Table
    $db->exec("CREATE TABLE IF NOT EXISTS industries (
        $pkType,
        name VARCHAR(255) NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        icon VARCHAR(100) NOT NULL,
        sort_order INTEGER DEFAULT 0
    )");
    
    // Seed Settings Table
    $db->exec("$insertIgnore INTO settings (`key`, value) VALUES 
        ('contact_phone', '+91-9718117270'),
        ('contact_email', 'sales@localglobal.com'),
        ('popup_status', 'hide'),
        ('popup_type', 'both'),
        ('popup_title', 'Welcome to Local Global Services!'),
        ('popup_text', 'Discover our next-generation SAP solutions and cognitive AI integrations.'),
        ('popup_image', '')
    ");
    
    // Seed Testimonials Table if empty
    $testimonialsCount = $db->query("SELECT COUNT(*) FROM testimonials")->fetchColumn();
    if ($testimonialsCount == 0) {
        $db->exec("INSERT INTO testimonials (client_name, service_name, testimonial_text, sort_order) VALUES 
            ('Sarah Jenkins', 'SAP S/4HANA Cloud', 'LGS executed our global S/4HANA brownfield migration with zero operations downtime. Truly an elite Gold partner.', 10),
            ('Ahmed Al-Sayed', 'RISE with SAP & IoT', 'Our fleet operations OEE increased by 22% within 90 days of deploying LGS cellular tracking telemetry.', 20),
            ('Michael Chen', 'AI AP Invoice Automation', 'The cognitive AP invoice automation has reduced our manual invoice processing times by 80%. Outstanding ROI!', 30)
        ");
    }

    // Seed Industries Table if empty
    $industriesCount = $db->query("SELECT COUNT(*) FROM industries")->fetchColumn();
    if ($industriesCount == 0) {
        $db->exec("INSERT INTO industries (name, title, description, icon, sort_order) VALUES 
            ('Manufacturing', 'Manufacturing', 'Optimize shop floor automation, implement advanced OEE dashboards, and track materials using localized SAP PLM modules.', 'fa-industry', 10),
            ('Retail & Consumer', 'Retail & Consumer', 'Integrate unified omni-channel customer experiences, coordinate supply networks, and automate restocking channels.', 'fa-basket-shopping', 20),
            ('Energy & Utilities', 'Energy & Utilities', 'Ensure smart grid integrations, optimize resource exploration processes, and manage global asset life cycles.', 'fa-bolt', 30),
            ('Logistics & Supply', 'Logistics & Supply', 'Maximize fleet utilization, integrate real-time cellular tracking telemetry, and lower overall warehousing overheads.', 'fa-truck-fast', 40)
        ");
    }
    
    // Seed Blogs Table if empty
    $blogsCount = $db->query("SELECT COUNT(*) FROM blogs")->fetchColumn();
    if ($blogsCount == 0) {
        $blogSeeds = [
            [
                'title' => 'Accelerating Enterprise Value with SAP S/4HANA Cloud',
                'slug' => 'accelerating-enterprise-value-sap-s4hana',
                'summary' => 'Explore how migrating to SAP S/4HANA Cloud empowers organizations with real-time analytics, automated workflows, and lower total cost of ownership.',
                'content' => '<p>Modern enterprises operate in a highly dynamic and hyper-competitive global market. Legacy ERP systems, once the bedrock of business operations, often struggle to keep pace with today\'s demands for real-time visibility, agility, and speed. This is where <strong>SAP S/4HANA Cloud</strong> steps in, offering a next-generation cloud-ERP platform that transforms operations.</p><h4>Why Migration is a Strategic Imperative</h4><p>Migrating to SAP S/4HANA Cloud is not merely an IT upgrade; it is a profound business transformation. By shifting core databases to the in-memory SAP HANA database, organizations can process massive volumes of operational data instantly. This enables real-time reporting, immediate financial close cycles, and automated predictive analytics.</p><h4>Key Benefits of S/4HANA Cloud</h4><ul><li><strong>Enhanced Agility:</strong> Instantly adapt to market changes with intelligent forecasting and integrated supplier networks.</li><li><strong>Reduced TCO:</strong> Shift from heavy capital expenditures to a predictable operational expense model with cloud hosting.</li><li><strong>Standardized Processes:</strong> Leverage built-in best practices for finance, HR, procurement, and manufacturing to eliminate custom code complexity.</li></ul><p>Partnering with a certified global integrator like Local Global Services (LGS) ensures a smooth transition using greenfield or brownfield migration strategies, minimizing downtime and accelerating time-to-value.</p>',
                'seo_title' => 'SAP S/4HANA Cloud Migration Benefits & Strategy | LGS',
                'meta_description' => 'Discover the strategic advantages of migrating to SAP S/4HANA Cloud. Learn how real-time databases and standardized cloud-ERP systems drive enterprise agility.',
                'image_url' => 'https://res.cloudinary.com/dbxfs9npx/image/upload/v1717672200/sap_s4hana_blog.jpg'
            ],
            [
                'title' => 'Industrial IoT: Bridging the Gap Between Factory Floors and Cloud Ledgers',
                'slug' => 'industrial-iot-factory-floor-to-cloud',
                'summary' => 'Discover how connecting machinery PLCs and IoT edge sensors with your core ERP drives real-time OEE visibility and proactive maintenance scheduling.',
                'content' => '<p>For decades, the factory floor and the corporate boardroom existed in separate silos. High-frequency machinery logs, temperature values, and production counts were tracked locally on standalone screens, while finance and planning sat in centralized databases. Today, <strong>Industrial IoT (IIoT)</strong> connects these two worlds, creating a unified flow of operational intelligence.</p><h4>The Power of Edge Analytics</h4><p>By deploying ruggedized sensors, cellular gateway transceivers, and PLC data collectors, manufacturers can capture raw telemetry from machinery in real-time. Edge gateways process this data immediately to calculate Overall Equipment Effectiveness (OEE), flag thermal drift, or detect motor vibration anomalies.</p><h4>Seamless Integration with SAP ERP</h4><p>When IoT data flows directly into the core ERP, the business benefits are immediate:</p><ul><li><strong>Predictive Maintenance:</strong> Automatically trigger maintenance work orders in SAP PM when a machine exceeds standard operating thresholds, preventing costly breakdown downtime.</li><li><strong>Automated Production Auditing:</strong> Product counts are updated in the inventory ledger instantly as they roll off the assembly line, avoiding manual count errors.</li><li><strong>Resilient Supply Chain:</strong> Link machine output directly to raw material procurement queues.</li></ul><p>By connecting hardware with enterprise data streams, businesses gain complete transparency and achieve true manufacturing excellence.</p>',
                'seo_title' => 'Industrial IoT and ERP Integration Guide | LGS',
                'meta_description' => 'Learn how Industrial IoT (IIoT) integrates factory floor telemetry and machine learning with ERP databases to automate maintenance and track real-time OEE.',
                'image_url' => 'https://res.cloudinary.com/dbxfs9npx/image/upload/v1717672200/iot_manufacturing_blog.jpg'
            ],
            [
                'title' => 'Generative AI and Cognitive Workflows in Modern Finance',
                'slug' => 'generative-ai-cognitive-workflows-finance',
                'summary' => 'How cognitive document models and machine learning classifiers automate accounts payable processing, eliminating manual invoice entry errors.',
                'content' => '<p>Accounts Payable (AP) departments are traditionally burdened with processing hundreds of invoices daily. Manual data entry, finding billing mismatches, and route approvals slow down cash cycles and increase compliance risks. <strong>Cognitive AI and Generative Models</strong> are rewriting this script, turning document entry into a touchless process.</p><h4>The Anatomy of Cognitive AP Automation</h4><p>Modern AP automation solutions leverage advanced machine learning models trained on millions of document types. These models do not rely on fragile coordinate-based templates. Instead, they read invoices contextually, extracting billing headers, vendor credentials, line items, and tax breakdowns with near-perfect accuracy.</p><h4>Achieving Three-Way Automated Matching</h4><p>Once the data is extracted, cognitive workflows integrate with core databases to execute three-way matches automatically:</p><ol><li>Verify the invoice against the original <strong>Purchase Order (PO)</strong> raised in the ERP.</li><li>Compare the items and quantities against the <strong>Goods Receipt (GR)</strong> recorded at the warehouse gate.</li><li>Automatically approve the invoice for payment if all values align, flagging exceptions for human review.</li></ol><p>LGS deploys intelligent invoice automation suites that connect seamlessly to SAP ERP databases, helping businesses reduce processing time by up to 80% while maintaining absolute audit transparency.</p>',
                'seo_title' => 'AI Invoice & Accounts Payable Automation | LGS',
                'meta_description' => 'Explore how cognitive machine learning models and AI-driven invoice automation eliminate manual data entry errors and accelerate accounts payable workflows.',
                'image_url' => 'https://res.cloudinary.com/dbxfs9npx/image/upload/v1717672200/ai_finance_blog.jpg'
            ]
        ];
        
        $insertBlog = $db->prepare("INSERT INTO blogs 
            (title, slug, summary, content, seo_title, meta_description, image_url) 
            VALUES (:title, :slug, :summary, :content, :seo_title, :meta_description, :image_url)
        ");
        
        foreach ($blogSeeds as $blog) {
            $insertBlog->bindParam(':title', $blog['title']);
            $insertBlog->bindParam(':slug', $blog['slug']);
            $insertBlog->bindParam(':summary', $blog['summary']);
            $insertBlog->bindParam(':content', $blog['content']);
            $insertBlog->bindParam(':seo_title', $blog['seo_title']);
            $insertBlog->bindParam(':meta_description', $blog['meta_description']);
            $insertBlog->bindParam(':image_url', $blog['image_url']);
            $insertBlog->execute();
        }
    }

    // Check if admin table is empty, if so, seed default admin: admin / admin123
    $stmt = $db->query("SELECT COUNT(*) FROM admins");
    if ($stmt->fetchColumn() == 0) {
        $username = 'admin';
        $password = password_hash('admin123', PASSWORD_BCRYPT);
        $seedAdmin = $db->prepare("INSERT INTO admins (username, password) VALUES (:username, :password)");
        $seedAdmin->bindParam(':username', $username);
        $seedAdmin->bindParam(':password', $password);
        $seedAdmin->execute();
    }
    
    // Reset and Seed Menus if empty
    $menuCount = $db->query("SELECT COUNT(*) FROM menus")->fetchColumn();
    if ($menuCount == 0) {
        // Parent Categories
        $db->exec("INSERT INTO menus (id, name, sort_order) VALUES 
            (1, 'About Us', 10),
            (2, 'Product & Services', 20),
            (3, 'Industries', 30),
            (4, 'SAP Solutions', 40)
        ");
        
        // Seed Submenus / Services
        $submenusSeed = [
            // Menu 1: About Us
            [
                'menu_id' => 1,
                'name' => 'Company Overview',
                'service_key' => 'about-overview',
                'tagline' => 'Your Trusted Technology Integrator & Digital Transformation Catalyst.',
                'icon' => 'fa-globe',
                'desc1' => 'Local Global Services (LGS) bridges the gap between traditional operations and modern digital technology stacks. Backed by twenty years of global consulting experience, we help enterprises design, build, and support their core operational systems.',
                'desc2' => 'We focus on building customer-centric digital solutions using high-performing tools like SAP ERP, cellular IoT tracking, and cognitive AI tools. Our dedicated team of 500+ certified specialists is focused on driving tangible business outcomes, lower TCO, and unmatched agility.',
                'features' => "Global Delivery Excellence\nTailored Industry Blueprints\nContinuous Support & Training",
                'banner_grad' => 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)',
                'sort_order' => 10
            ],
            
            // Menu 2: Product & Services
            [
                'menu_id' => 2,
                'name' => 'SAP S/4HANA & Digital Core',
                'service_key' => 'sap-s4hana',
                'tagline' => 'Modernize your enterprise with next-generation smart ERP systems.',
                'icon' => 'fa-server',
                'desc1' => 'SAP S/4HANA is the future-ready enterprise resource planning (ERP) system designed specifically for the digital age. Built on our advanced in-memory SAP HANA database, it enables businesses to perform transactions, run real-time analytics, and make critical decisions instantly.',
                'desc2' => 'At Local Global Services (LGS), we guide global enterprises through seamless S/4HANA implementations, greenfield migrations, and system conversions. Our certified consultants optimize your corporate workflows to achieve hyper-growth, lower operational costs, and unprecedented data speed.',
                'features' => "End-to-end greenfield and brownfield system conversions.\nReal-time automated financial consolidations and instant accounting closes.\nIntelligent inventory management with predictive material resource planning (MRP).\nSleek, personalized user experiences utilizing SAP Fiori layouts.",
                'banner_grad' => 'linear-gradient(135deg, #0B356D 0%, #1e3a8a 100%)',
                'sort_order' => 10
            ],
            [
                'menu_id' => 2,
                'name' => 'RISE with SAP',
                'service_key' => 'rise-sap',
                'tagline' => 'Your accelerated path to a cloud-driven intelligent enterprise.',
                'icon' => 'fa-cloud-arrow-up',
                'desc1' => 'RISE with SAP is a comprehensive, single-contract SaaS bundle designed to take your enterprise operating system to the cloud. It combines cloud ERP, business process intelligence, custom network integrations, and advanced technical migration support.',
                'desc2' => 'LGS acts as your co-pilot on the RISE journey. We manage the cloud infrastructure scaling, simplify custom application integrations, and optimize your systems, ensuring a zero-downtime, secure migration to AWS, Azure, or GCP under SAP control.',
                'features' => "Unified cloud ERP hosting with SLA-backed, secure infrastructure.\nBusiness Process Intelligence (BPI) to discover and fix operational bottlenecks.\nFull access to the global SAP Business Network for collaborative procurement.\nContinuous technical management and automated software upgrades.",
                'banner_grad' => 'linear-gradient(135deg, #0B356D 0%, #0284c7 100%)',
                'sort_order' => 20
            ],
            [
                'menu_id' => 2,
                'name' => 'GROW with SAP',
                'service_key' => 'grow-sap',
                'tagline' => 'Empowering mid-sized businesses with agile cloud ERP.',
                'icon' => 'fa-chart-line',
                'desc1' => 'GROW with SAP is designed specifically to help mid-sized, growing companies adopt cloud ERP quickly and predictably. It offers speed, scalability, and built-in industry best practices, allowing you to run your finance, sales, and purchasing operations globally.',
                'desc2' => 'Our team at LGS specializes in executing rapid GROW with SAP rollouts in as little as 4 to 8 weeks. We eliminate custom development complexities by utilizing SAP standard templates, saving you capital while delivering a world-class operating system.',
                'features' => "Rapid deployment blueprints tailored for rapid scaling.\nBuilt-in localized tax, language, and compliance engines for global trade.\nPredictive cash flow modeling and automated supplier matching.\nZero custom coding requirement - fully standardized and upgradable.",
                'banner_grad' => 'linear-gradient(135deg, #0B356D 0%, #10b981 100%)',
                'sort_order' => 30
            ],
            [
                'menu_id' => 2,
                'name' => 'Continuous Application Management',
                'service_key' => 'continuous-ams',
                'tagline' => 'Proactive support and maintenance for critical IT & SAP layers.',
                'icon' => 'fa-user-gear',
                'desc1' => 'Continuous Application Management Services (AMS) ensure that your enterprise systems, custom applications, and cloud environments perform at maximum efficiency 24/7. It covers monitoring, bug resolution, performance optimizations, and security patches.',
                'desc2' => 'LGS delivers peace of mind with our dedicated global support centers. Our certified technicians act as an extension of your IT department, monitoring database queues, optimizing server compute, and resolving application glitches before they impact operations.',
                'features' => "24/7 system monitoring with guaranteed, SLA-backed response times.\nProactive database index tuning and server performance scaling.\nRegular security updates, compliance patching, and data backups.\nCustom report generation and automated monthly performance analysis.",
                'banner_grad' => 'linear-gradient(135deg, #0B356D 0%, #475569 100%)',
                'sort_order' => 40
            ],
            [
                'menu_id' => 2,
                'name' => 'SAP SuccessFactors',
                'service_key' => 'sap-successfactors',
                'tagline' => 'Transforming HR into a strategic human experience management (HXM) engine.',
                'icon' => 'fa-people-group',
                'desc1' => 'SAP SuccessFactors is the cloud-based Human Experience Management (HXM) solution. Moving beyond traditional HR, it connects core payroll, talent acquisition, performance reviews, employee learning, and workforce analytics into a single employee-centric platform.',
                'desc2' => 'LGS designs HR transformation strategies that improve retention, automate global payroll, and elevate employee engagement. We seamlessly integrate SuccessFactors with your core SAP ERP to ensure complete organizational synchronization.',
                'features' => "Global Employee Central for compliant, localized payroll and time tracking.\nAI-guided recruitment and onboarding pipelines to source elite talent.\nComprehensive performance mapping, continuous feedback, and compensation systems.\nRobust workforce analytics for data-backed succession planning.",
                'banner_grad' => 'linear-gradient(135deg, #0B356D 0%, #7c3aed 100%)',
                'sort_order' => 50
            ],
            [
                'menu_id' => 2,
                'name' => 'SAP Ariba',
                'service_key' => 'sap-ariba',
                'tagline' => 'Digitize your global supply chain and procurement networks.',
                'icon' => 'fa-truck-ramp-box',
                'desc1' => 'SAP Ariba is the world\'s leading B2B marketplace and procurement platform. It digitalizes the entire source-to-pay lifecycle, allowing organizations to manage sourcing, contract negotiations, supplier risk, purchasing, and invoicing in a single interface.',
                'desc2' => 'We help procurement departments build highly resilient supply chains. By integrating SAP Ariba with your backend inventory systems, LGS ensures transparent supplier relationships, reduces spending leakages, and drives corporate compliance.',
                'features' => "Seamless connection to the SAP Business Network representing millions of sellers.\nAutomated Source-to-Contract suites to streamline negotiations.\nComprehensive supplier risk profiling and performance auditing tools.\nAriba Guided Buying to ensure corporate policy adherence across departments.",
                'banner_grad' => 'linear-gradient(135deg, #0B356D 0%, #db2777 100%)',
                'sort_order' => 60
            ],
            [
                'menu_id' => 2,
                'name' => 'Business Analytics & Power BI',
                'service_key' => 'analytics-powerbi',
                'tagline' => 'Transform raw data silos into visual intelligence.',
                'icon' => 'fa-chart-pie',
                'desc1' => 'Modern enterprises generate massive volumes of data, but without structured analysis, it remains dark. Business Analytics utilizing Microsoft Power BI, SAP Analytics Cloud, and advanced warehouse layers allows you to visualize trends, forecast demand, and make data-driven decisions.',
                'desc2' => 'LGS builds enterprise business intelligence systems. We connect disparate CRM, ERP, and IoT data feeds, construct automated ETL pipelines, and create stunning interactive dashboards that allow executives to inspect details from high-level summaries.',
                'features' => "Automated data connection pipelines mapping disparate database types.\nStunning, real-time interactive visualization dashboards.\nPredictive trend forecasting utilizing built-in Machine Learning algorithms.\nMobile-friendly dashboards with automated, scheduled email reports.",
                'banner_grad' => 'linear-gradient(135deg, #0B356D 0%, #b45309 100%)',
                'sort_order' => 70
            ],
            [
                'menu_id' => 2,
                'name' => 'IoT Production Tracking',
                'service_key' => 'iot-production',
                'tagline' => 'Connecting factory hardware to the cloud for real-time OEE visibility.',
                'icon' => 'fa-industry',
                'desc1' => 'Internet of Things (IoT) in manufacturing bridges physical hardware with cloud databases. By mounting smart sensors, PLC collectors, and edge computers on the assembly lines, managers can track machine uptime, count products, and measure Overall Equipment Effectiveness (OEE) in real-time.',
                'desc2' => 'LGS constructs end-to-end industrial IoT solutions. We install ruggedized hardware collectors, deploy cloud ingest brokers, and compile real-time telemetry into modern shop-floor dashboards, allowing engineers to prevent bottlenecks instantly.',
                'features' => "Real-time Overall Equipment Effectiveness (OEE) calculation and displays.\nPredictive maintenance alerts triggered by motor vibration and temperature drift.\nAutomated product count audits directly integrated with warehouse databases.\nLow-latency, secure industrial network integrations.",
                'banner_grad' => 'linear-gradient(135deg, #0B356D 0%, #065f46 100%)',
                'sort_order' => 80
            ],
            [
                'menu_id' => 2,
                'name' => 'Vehicle Live Tracking System',
                'service_key' => 'iot-vehicle',
                'tagline' => 'Real-time GPS fleet telemetry and logistics optimization.',
                'icon' => 'fa-truck-fast',
                'desc1' => 'Fleet logistics operations require continuous coordination. A Vehicle Live Tracking System combines rugged GPS trackers, cellular transceivers, and specialized web consoles to plot vehicle location, speed, fuel utilization, and sensor values dynamically.',
                'desc2' => 'Our LGS IoT team deploys enterprise-grade fleet tracking systems. We supply cellular tracking devices, integrate OBD-II telemetry, and build intelligent routing engines that help distribution centers lower fuel costs and ensure secure deliveries.',
                'features' => "Real-time GPS geofencing and instant speed/idle threshold alerts.\nDirect engine OBD-II diagnostics to schedule preemptive servicing.\nIntelligent, traffic-aware dispatch routing and accurate ETA estimation.\nDriver safety scoring and tracking.",
                'banner_grad' => 'linear-gradient(135deg, #0B356D 0%, #0369a1 100%)',
                'sort_order' => 90
            ],
            [
                'menu_id' => 2,
                'name' => 'Enterprise Vendor Portal',
                'service_key' => 'app-vendor-portal',
                'tagline' => 'Streamline procurement and supplier relationships securely.',
                'icon' => 'fa-laptop-code',
                'desc1' => 'An Enterprise Vendor Portal is a secure, web-based platform that allows external suppliers to submit bids, review purchase orders, update shipping statuses, upload digital invoices, and update compliance certificates autonomously.',
                'desc2' => 'LGS builds customized, highly secure supplier portals that connect directly to your core SAP ERP. By automating PO-to-Invoice matching and supplier self-registration, we eliminate manual email exchanges, reduce entry errors, and lower procurement costs.',
                'features' => "Secure multi-factor supplier authentication and profile management.\nAutomated purchase order transmission and invoice uploads.\nReal-time PO-to-GR-to-Invoice (3-way) matching algorithms.\nSecure document storage for active compliance certificates.",
                'banner_grad' => 'linear-gradient(135deg, #0B356D 0%, #5b21b6 100%)',
                'sort_order' => 100
            ],
            [
                'menu_id' => 2,
                'name' => 'AI AP Invoice Automation',
                'service_key' => 'ai-invoice-automation',
                'tagline' => 'Eliminate manual entry with cognitive invoice processing.',
                'icon' => 'fa-brain',
                'desc1' => 'Accounts Payable departments are often bogged down by manual document entry, paper chasing, and exception sorting. AI Invoice Automation leverages advanced optical character recognition (OCR), machine learning classifiers, and workflow automation to process invoices hands-free.',
                'desc2' => 'We deploy cognitive document models that extract billing headers, line items, and tax breakdowns from PDF invoices automatically. LGS integrates these AI tools directly with SAP or other ERP databases to ensure instant PO matching and payment approvals.',
                'features' => "Cognitive OCR extraction from multi-page PDFs with 99%+ accuracy.\nSelf-learning classification models that adapt to unique supplier layouts.\nAutomated 3-way matching against purchase orders and receipts.\nCustom review dashboards for quick invoice exception handling.",
                'banner_grad' => 'linear-gradient(135deg, #0B356D 0%, #9d174d 100%)',
                'sort_order' => 110
            ],
            [
                'menu_id' => 2,
                'name' => 'Enterprise Cyber Security',
                'service_key' => 'cyber-security',
                'tagline' => 'Defend your digital assets with advanced threat intelligence.',
                'icon' => 'fa-shield-halved',
                'desc1' => 'As operations transition to the cloud, securing applications, APIs, databases, and network layers against sophisticated cyber threats is paramount. A single breach can cause massive financial damages, data losses, and reputational ruin.',
                'desc2' => 'LGS builds enterprise cyber defense networks. We conduct thorough vulnerability audits, deploy zero-trust security architectures, integrate threat detection systems, and establish secure IAM rules, ensuring complete data security and regulatory compliance.',
                'features' => "Comprehensive penetration testing and network vulnerability assessments.\nZero-Trust network architecture and multi-factor authentication setup.\n24/7 security event monitoring and incident response coordination.\nStrict compliance alignment with GDPR, HIPAA, and ISO 27001 standards.",
                'banner_grad' => 'linear-gradient(135deg, #0B356D 0%, #1e1b4b 100%)',
                'sort_order' => 120
            ],
            
            // Menu 3: Industries
            [
                'menu_id' => 3,
                'name' => 'Automotive',
                'service_key' => 'ind-automotive',
                'tagline' => 'Accelerating smart assembly lines and parts logistic channels.',
                'icon' => 'fa-car',
                'desc1' => 'The automotive industry requires absolute coordination across just-in-time delivery channels, raw materials tracing, and automated assembly equipment. Legacy paper sheets or disjointed software networks create severe lag and bottleneck risks.',
                'desc2' => 'LGS supplies dynamic enterprise architectures for automotive OEMs. We connect shop-floor PLCs, orchestrate material demands, and integrate core SAP Automotive templates to optimize material replenishments, leading to minimum warehouse overheads.',
                'features' => "Just-in-Time (JIT) and Just-in-Sequence (JIS) supply logistics.\nFull hardware PLC connectivity for assembly speed audits.\nInstant serial tracking across global supplier networks.",
                'banner_grad' => 'linear-gradient(135deg, #0B356D 0%, #0369a1 100%)',
                'sort_order' => 10
            ],
            [
                'menu_id' => 3,
                'name' => 'Life Sciences & Pharma Tech',
                'service_key' => 'ind-lifesciences',
                'tagline' => 'Accelerating regulatory compliance, clinical trials, and cold chains.',
                'icon' => 'fa-prescription-bottle-medical',
                'desc1' => 'The life sciences and pharmaceutical sectors face strict regulatory rules, GMP tracking requirements, and complex clinical development stages.',
                'desc2' => 'LGS integrates advanced Life Sciences validation templates with SAP ERP to streamline electronic batch records, temperature-sensitive supply chains, and batch tracking.',
                'features' => "Compliant electronic batch records (EBR)\nFDA Title 21 CFR Part 11 validation workflows\nSecure clinical batch tracking",
                'banner_grad' => 'linear-gradient(135deg, #0B356D 0%, #7c3aed 100%)',
                'sort_order' => 20
            ],
            [
                'menu_id' => 3,
                'name' => 'Engineering, Procurement & Construction (EPC)',
                'service_key' => 'ind-epc',
                'tagline' => 'Visualizing project milestones, procurement pipelines, and cost structures.',
                'icon' => 'fa-trowel-bricks',
                'desc1' => 'Major engineering and construction projects require continuous tracking of material demands, subcontractor pipelines, and milestone payments.',
                'desc2' => 'LGS builds digital EPC control centers linking on-site measurements directly to corporate ledgers, optimizing working capital and preventing delays.',
                'features' => "Subcontractor measurement log integrations\nEarned Value Management (EVM) tracking\nReal-time equipment utilization audits",
                'banner_grad' => 'linear-gradient(135deg, #0B356D 0%, #475569 100%)',
                'sort_order' => 30
            ],
            [
                'menu_id' => 3,
                'name' => 'Energy & Utility Services',
                'service_key' => 'ind-energy',
                'tagline' => 'Securing power grid assets, operations uptime, and customer billing.',
                'icon' => 'fa-bolt',
                'desc1' => 'Energy generation and utility grids manage massive critical hardware assets, preventative repairs, and complex consumer invoicing grids.',
                'desc2' => 'LGS designs secure asset tracking databases and grid maintenance scheduling tools, ensuring high performance, minimum breakdowns, and billing precision.',
                'features' => "Preventative grid maintenance scheduling\nSmart meter billing integrations\nStrict ISO 55001 asset integrity standards",
                'banner_grad' => 'linear-gradient(135deg, #0B356D 0%, #db2777 100%)',
                'sort_order' => 40
            ],
            [
                'menu_id' => 3,
                'name' => 'SAP for Manufacturing Excellence',
                'service_key' => 'ind-manufacturing',
                'tagline' => 'Empowering smart shop-floors with high-performing cloud ERP.',
                'icon' => 'fa-industry',
                'desc1' => 'Modern production plants require absolute synchronization across raw materials availability, machine metrics, and supply chains.',
                'desc2' => 'LGS deploys Manufacturing Execution Systems (MES) integrated with SAP S/4HANA, optimizing cycle times, lower scrap, and raising yields.',
                'features' => "Integrated Shop-Floor Control (SFC)\nReal-time Overall Equipment Effectiveness (OEE)\nAutomated material replenishment loops",
                'banner_grad' => 'linear-gradient(135deg, #0B356D 0%, #065f46 100%)',
                'sort_order' => 50
            ],
            [
                'menu_id' => 3,
                'name' => 'Real Estate',
                'service_key' => 'ind-realestate',
                'tagline' => 'Maximizing property yields, lease management, and visual billing.',
                'icon' => 'fa-building',
                'desc1' => 'Real estate management demands precise tracking of tenant lease histories, square-footage yields, maintenance expenses, and tax updates.',
                'desc2' => 'LGS deploys comprehensive SAP RE-FX modules, streamlining property asset onboarding, contract updates, and rent reconciliations.',
                'features' => "Automated contract renewals and billing alerts\nDetailed property expense tracking models\nComprehensive rent calculation indexes",
                'banner_grad' => 'linear-gradient(135deg, #0B356D 0%, #b45309 100%)',
                'sort_order' => 60
            ],
            [
                'menu_id' => 3,
                'name' => 'Retail Transformation Services',
                'service_key' => 'ind-retail',
                'tagline' => 'Unifying point-of-sale systems, warehouses, and e-commerce layers.',
                'icon' => 'fa-basket-shopping',
                'desc1' => 'Omnichannel retail operations demand real-time stock balances across virtual carts, regional centers, and retail stores.',
                'desc2' => 'LGS constructs retail backbones integrating POS terminals with SAP Retail layers, accelerating checkout speed and fulfillment.',
                'features' => "Omnichannel inventory synchronization\nIntegrated Point-of-Sale (POS) pipelines\nPredictive demand replenishment planning",
                'banner_grad' => 'linear-gradient(135deg, #0B356D 0%, #0369a1 100%)',
                'sort_order' => 70
            ],
            
            // Menu 4: SAP Solutions
            [
                'menu_id' => 4,
                'name' => 'GST Reconciliation',
                'service_key' => 'gst-reconciliation',
                'tagline' => 'Ensure complete tax compliance through direct portal integrations.',
                'icon' => 'fa-receipt',
                'desc1' => 'Tax compliance represents a challenge, requiring e-invoicing generation, GST return reconciliation, and digital signatures.',
                'desc2' => 'LGS provides GST e-Invoice solutions. We build secure, real-time middleware that connects your ERP directly to the government tax portal, automating invoice IRN generation, QR code mapping, and GST reconciling.',
                'features' => "Instant e-Invoice generation directly from sales orders.\nAutomated e-Waybill generation utilizing real-time dispatch details.\nRobust 2B reconciliation algorithms to recover maximum input tax credits.\nSecure API tunnels for tax authority communications.",
                'banner_grad' => 'linear-gradient(135deg, #0B356D 0%, #0369a1 100%)',
                'sort_order' => 10
            ],
            [
                'menu_id' => 4,
                'name' => 'SAP e-Invoice Solution',
                'service_key' => 'sol-e-invoice',
                'tagline' => 'Automate government tax portal registrations directly from billing logs.',
                'icon' => 'fa-receipt',
                'desc1' => 'Modern tax authorities require companies to submit dynamic e-invoices instantly on sales execution, generating unique Government IRNs.',
                'desc2' => 'LGS builds secure API tunnels between SAP and government tax gateways, automating invoice declarations, QR prints, and logs.',
                'features' => "Zero-latency Government gateway communication\nAutomatic QR code generation on bill prints\nFull audit trails and error logging",
                'banner_grad' => 'linear-gradient(135deg, #0B356D 0%, #0284c7 100%)',
                'sort_order' => 20
            ],
            [
                'menu_id' => 4,
                'name' => 'SAP e-Waybill Solution',
                'service_key' => 'sol-e-waybill',
                'tagline' => 'Streamline dispatch logistics with instant transit certificates.',
                'icon' => 'fa-truck-fast',
                'desc1' => 'Transporting physical goods requires instantaneous e-waybill document creation to prevent cargo seizures and delays.',
                'desc2' => 'LGS integrates transit logs directly with warehouse dispatch records, generating valid e-waybills automatically upon shipment.',
                'features' => "Automated vehicle details validation\nInstant bulk e-waybill generation\nDirect vehicle break-down alerts",
                'banner_grad' => 'linear-gradient(135deg, #0B356D 0%, #10b981 100%)',
                'sort_order' => 30
            ],
            [
                'menu_id' => 4,
                'name' => 'SAP Gate Entry Exit Solution',
                'service_key' => 'sol-gate-entry',
                'tagline' => 'Secure industrial warehouse yards with digital check-ins.',
                'icon' => 'fa-door-open',
                'desc1' => 'Logistics yards suffer from manual logs, untracked truck entries, and long processing times at vehicle checkpoints.',
                'desc2' => 'LGS builds digital Gate Entry applications integrated with SAP, validating purchase orders and weighbridge measurements dynamically.',
                'features' => "Automated purchase order check-in validation\nDirect driver biometric and vehicle logging\nSeamless weighbridge balance integrations",
                'banner_grad' => 'linear-gradient(135deg, #0B356D 0%, #475569 100%)',
                'sort_order' => 40
            ],
            [
                'menu_id' => 4,
                'name' => 'Digital Signature Integration',
                'service_key' => 'sol-digital-sig',
                'tagline' => 'Ensure document authenticity through secure cryptographic signs.',
                'icon' => 'fa-signature',
                'desc1' => 'Manual corporate approvals, customer contracts, and supplier invoices require secure validation controls to prevent fraud.',
                'desc2' => 'LGS embeds advanced cryptographic signing modules (e.g. DSC, Aadhaar, DocuSign) directly into your ERP workflow queues.',
                'features' => "Cryptographic multi-factor signing queues\nAutomated batch signing for bills\nFully compliant audit trails",
                'banner_grad' => 'linear-gradient(135deg, #0B356D 0%, #7c3aed 100%)',
                'sort_order' => 50
            ],
            [
                'menu_id' => 4,
                'name' => 'Barcode Integration',
                'service_key' => 'sol-barcode',
                'tagline' => 'Raise warehouse scan speeds using advanced RFID and barcode tags.',
                'icon' => 'fa-barcode',
                'desc1' => 'Manual inventory count sheets are slow, introduce severe calculation errors, and delay order shipments.',
                'desc2' => 'LGS integrates industrial scanners, QR code builders, and RFID targets with your warehouse management systems.',
                'features' => "Direct barcode scan-to-issue inventories\nRFID long-range tag detection grids\nAutomated packing-slip calculations",
                'banner_grad' => 'linear-gradient(135deg, #0B356D 0%, #db2777 100%)',
                'sort_order' => 60
            ],
            [
                'menu_id' => 4,
                'name' => 'WhatsApp Integration',
                'service_key' => 'sol-whatsapp',
                'tagline' => 'Deliver automated transactional alerts directly to customer chat screens.',
                'icon' => 'fa-message',
                'desc1' => 'Customers and suppliers demand immediate visibility of order statuses, ledger statements, and payments.',
                'desc2' => 'LGS develops custom notification triggers linking ERP sales cycles with official WhatsApp Business API gateways.',
                'features' => "Automated sales order confirmation alerts\nDynamic invoice PDF deliveries in chat\nAutomated conversational AI helper bots",
                'banner_grad' => 'linear-gradient(135deg, #0B356D 0%, #b45309 100%)',
                'sort_order' => 70
            ],
            [
                'menu_id' => 4,
                'name' => 'Payment Gateway Integration',
                'service_key' => 'sol-payment',
                'tagline' => 'Accelerate corporate collections through secure digital payments.',
                'icon' => 'fa-credit-card',
                'desc1' => 'B2B collections suffer from manual checks, wire tracing, and slow invoice reconciliation loops.',
                'desc2' => 'LGS embeds secure payment gateways (Stripe, Razorpay) into digital invoices, letting buyers pay instantly and reconcile automatically.',
                'features' => "Direct invoice payment link generation\nMulti-currency secure gateway support\nAutomated bank receipt reconciliation",
                'banner_grad' => 'linear-gradient(135deg, #0B356D 0%, #065f46 100%)',
                'sort_order' => 80
            ],
            [
                'menu_id' => 4,
                'name' => 'P2P Procurement Automation',
                'service_key' => 'sol-procurement',
                'tagline' => 'Eliminate manual purchase operations from sourcing to receipt.',
                'icon' => 'fa-cart-shopping',
                'desc1' => 'Procurement departments spend heavy time raising manual purchase requisitions, collecting bids, and tracking deliveries.',
                'desc2' => 'LGS builds cloud procurement portals that automate buyer workflows, bid reviews, and three-way document matches.',
                'features' => "Automated bid evaluation matrices\nSecure buyer approvals workflows\nInstant 3-way invoice matching checks",
                'banner_grad' => 'linear-gradient(135deg, #0B356D 0%, #0369a1 100%)',
                'sort_order' => 90
            ]
        ];
        
        $insertSub = $db->prepare("INSERT INTO submenus 
            (menu_id, name, service_key, tagline, icon, desc1, desc2, features, banner_grad, sort_order) 
            VALUES (:menu_id, :name, :service_key, :tagline, :icon, :desc1, :desc2, :features, :banner_grad, :sort_order)
        ");
        
        foreach ($submenusSeed as $sub) {
            $insertSub->bindParam(':menu_id', $sub['menu_id']);
            $insertSub->bindParam(':name', $sub['name']);
            $insertSub->bindParam(':service_key', $sub['service_key']);
            $insertSub->bindParam(':tagline', $sub['tagline']);
            $insertSub->bindParam(':icon', $sub['icon']);
            $insertSub->bindParam(':desc1', $sub['desc1']);
            $insertSub->bindParam(':desc2', $sub['desc2']);
            $insertSub->bindParam(':features', $sub['features']);
            $insertSub->bindParam(':banner_grad', $sub['banner_grad']);
            $insertSub->bindParam(':sort_order', $sub['sort_order']);
            $insertSub->execute();
        }
    }
    
    $seedMessage = "LGS Enterprise SQL Database initialized successfully.<br>Tables generated: <strong>submissions</strong>, <strong>admins</strong>, <strong>menus</strong>, <strong>submenus (services)</strong>.<br>Initial menus & 13 corporate service templates seeded successfully.<br><br>Default Admin Seed:<br>Username: <strong>admin</strong><br>Password: <strong>admin123</strong>";
    
    // Output response for setup verification
    echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-left: 5px solid #0B356D;'>";
    echo "<h2 style='color: #0B356D; margin-top: 0;'>Setup Status</h2>";
    echo "<p>{$seedMessage}</p>";
    echo "<a href='../index.html' style='display: inline-block; background: #0B356D; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Back to Homepage</a>";
    echo "</div>";
    
} catch (PDOException $e) {
    die("Database setup failed: " . $e->getMessage());
}
?>
