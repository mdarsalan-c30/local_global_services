-- LGS Enterprise MySQL Database Dump
-- Generated: 2026-06-12 07:48:15
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- Table structure for `submissions`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `submissions`;
CREATE TABLE IF NOT EXISTS submissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(50) DEFAULT NULL,
  service VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  status VARCHAR(50) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for `submissions`
INSERT INTO `submissions` (`id`, `name`, `email`, `phone`, `service`, `message`, `created_at`, `status`) VALUES
('1', 'md', 'priumacc@protonmail.com', NULL, 'Cloud &#38; AI Services', 'test', '2026-06-03 19:16:33', 'Replied'),
('2', 'md 2', 'priumacc@protonmail.com', NULL, 'Cloud &#38; AI Services', 'hey test 2', '2026-06-04 08:23:23', 'Replied'),
('3', 'Md Arsalan Arsalan', 'mdarsalankec@gmail.com', '09540705298', 'SAP S/4HANA &#38; RISE', 'b', '2026-06-08 12:57:27', 'Rejected'),
('4', 'test emal', 'hello@gmail.com', '9789878972', 'SAP S/4HANA &#38; RISE', 'sales@globals.com', '2026-06-08 13:46:58', 'Pending'),
('5', 'test primium', 'arsalan@gmail.vom', '6585485485', 'SuccessFactors', 'hello this is test to check email notifcaation ', '2026-06-08 13:50:24', 'Converted');

-- --------------------------------------------------------
-- Table structure for `admins`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for `admins`
INSERT INTO `admins` (`id`, `username`, `password`) VALUES
('1', 'admin@localglobals.com', '$2y$10$NP2AZYg5L5yEbiEVWOmOZOsOuR.6VdA.n8A47lycpr0SNdoFJ8sFS');

-- --------------------------------------------------------
-- Table structure for `menus`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `menus`;
CREATE TABLE IF NOT EXISTS menus (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  sort_order INTEGER DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for `menus`
INSERT INTO `menus` (`id`, `name`, `sort_order`) VALUES
('1', 'About', '10'),
('2', 'Product & Services', '20'),
('3', 'Industry-Driven SAP Solutions', '30'),
('4', 'SAP Solutions', '40'),
('5', 'News & Resources', '50');

-- --------------------------------------------------------
-- Table structure for `submenus`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `submenus`;
CREATE TABLE IF NOT EXISTS submenus (
  id INT AUTO_INCREMENT PRIMARY KEY,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for `submenus`
INSERT INTO `submenus` (`id`, `menu_id`, `name`, `service_key`, `tagline`, `icon`, `desc1`, `desc2`, `features`, `banner_grad`, `sort_order`, `image_url`) VALUES
('1', '1', 'Company Overview', 'about-overview', 'Your Trusted Technology Integrator & Digital Transformation Catalyst.', 'fa-globe', 'LGS delivers smart IT and AI solutions that optimize your business operations. From
ERP platforms to advanced infrastructure management, we give you the tools to
make better decisions and scale faster. Let us help your business thrive in the digital
age.', 'We focus on building customer-centric digital solutions using high-performing tools like SAP ERP, cellular IoT tracking, and cognitive AI tools.', 'Global Delivery Excellence
Tailored Industry Blueprints
Continuous Support & Training', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '10', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243179/localglobals/uploads/kpfrn0yrwtqu2o6zcey7.png'),
('2', '2', 'Integrated SAP & Digital Transformation Services', 'sap-digital-transform', 'Unified enterprise operational layers driving end-to-end digital integration.', 'fa-network-wired', 'LGS Integrated SAP & Digital Transformation services construct the backbone of modern corporate systems. We align business units with cloud architectures to eliminate operational silos, standardize processes, and enable secure, multi-country trade flows.', 'By combining industry-best practices with advanced system architectures, we help traditional manufacturing and logistics networks shift to real-time operations, providing rapid ROI and agility.', 'Business Process Harmonization
End-to-End Enterprise Blueprints
Multi-Country SAP Rollouts
Legacy Migration Strategies', 'linear-gradient(135deg, #0B356D 0%, #0c4a6e 100%)', '5', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243183/localglobals/uploads/ojfnmghhhmbxxji7wdym.jpg'),
('3', '2', 'SAP S/4HANA', 'sap-s4hana', 'Intelligent ERP for the Modern Enterprise', 'fa-server', 'SAP S/4HANA is SAP''s next-generation ERP platform built on the in-memory HANA database, enabling organisations to run finance, procurement, supply chain, manufacturing, and HR etc on a single unified system in real time.', 'Unlike legacy ERP, S/4HANA simplifies complexity through embedded analytics, intelligent automation, and a streamlined data model. Deployable on-premise, private cloud, or via RISE with SAP, it gives businesses the agility to respond faster and grow confidently.

Our certified consultants guide you through every phase — from business blueprinting and system configuration to data migration, testing, training, and go-live support.', 'Real-time processing and instant reporting
Simplified data model with lower storage costs
Embedded AI, machine learning, and predictive analytics
Single platform across Finance, Procurement, Supply Chain, Sales, MM, HR, PP, QM and EWM,
Flexible deployment — on-premise, private or public cloud
Clean-core architecture for risk-free innovation', 'linear-gradient(135deg, #0B356D 0%, #1e3a8a 100%)', '10', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243186/localglobals/uploads/idrht2gs1vz3lk3hxmlt.png'),
('4', '2', 'RISE with SAP', 'rise-sap', 'Business Transformation as a Service', 'fa-cloud-arrow-up', 'RISE with SAP is SAP''s all-in-one transformation offering that bundles S/4HANA Cloud, infrastructure, tools, and services into a single subscription — making enterprise transformation simpler, faster, and more cost-effective.', 'Rather than managing multiple vendors and contracts, RISE with SAP gives you everything under one agreement — cloud infrastructure, SAP Business Network access, embedded analytics, and continuous innovation updates delivered automatically.

Our consultants provide vendor-neutral advisory on commercial terms, technical transition planning, and business process redesign — ensuring you extract maximum value from your RISE investment from day one.', 'Single subscription bundling ERP, infrastructure, and services
Lower upfront investment with predictable monthly costs
Automatic quarterly innovations with no manual upgrades
Access to SAP Business Network and partner ecosystem
Hyperscaler flexibility — AWS, Azure, or Google Cloud
Clean-core principles enabling faster, lower-risk extensions
End-to-end support from commercial negotiation to go-live', 'linear-gradient(135deg, #0B356D 0%, #0284c7 100%)', '15', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243188/localglobals/uploads/ocqq0pi56uxlk2sy0qpt.jpg'),
('5', '2', 'GROW with SAP', 'grow-sap', 'Fast-Track ERP for Growing Businesses', 'fa-chart-line', 'GROW with SAP is designed for mid-market and fast-growing businesses that need the power of S/4HANA Cloud Public Edition with a rapid, standardised deployment. It delivers enterprise-grade ERP capabilities without the complexity and cost of a traditional implementation.', 'Built on best-practice business processes and preconfigured industry content, GROW with SAP enables companies to go live quickly, adopt SAP''s latest innovations continuously, and scale confidently as their business evolves.

Our consultants accelerate your journey using fit-to-standard workshops, agile sprint delivery, and proven implementation toolkits — getting you operational in as little as 12 weeks.', 'Purpose-built for mid-market and high-growth companies
Preconfigured best-practice processes for faster deployment
Go-live in as little as 12–16 weeks
Subscription-based model with low upfront investment
Continuous quarterly innovation updates automatically delivered
Clean-core architecture with SAP BTP extensibility
Agile, sprint-based delivery with weekly stakeholder reviews', 'linear-gradient(135deg, #0B356D 0%, #10b981 100%)', '20', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243191/localglobals/uploads/q109cm0nfqqmm7gk4jbz.png'),
('6', '2', 'Continuous Application Management', 'continuous-ams', 'Keep SAP Running at Its Best', 'fa-user-gear', 'Continuous Application Management (CAM) ensures your SAP environment remains stable, secure, and continuously optimised long after go-live. Rather than reacting to issues, our managed service takes a proactive approach — monitoring, maintaining, and improving your SAP landscape around the clock.', 'Our dedicated team of SAP specialists acts as an extension of your internal IT function, handling everything from incident resolution and system monitoring to security patching and performance tuning — freeing your team to focus on strategic priorities rather than day-to-day operations.

With defined SLAs, transparent reporting, and regular service reviews, you always have full visibility of your SAP environment''s health and performance.', '24/7 proactive monitoring across all SAP modules and integrations
15-minute response time for Priority 1 critical incidents
Structured incident, problem, and change management processes
Regular security patching and authorisation reviews
Continuous improvement recommendations and enhancement cycles
Monthly service reports with real-time performance dashboards
Flexible service tiers from light-touch support to full outsource
Compliance support for GDPR, ISO 27001 and ISO 9001', 'linear-gradient(135deg, #0B356D 0%, #475569 100%)', '25', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243193/localglobals/uploads/qigfqv7edutq2kgdn8kb.jpg'),
('7', '2', 'Migration & Upgrade', 'sap-migration', 'Move Forward Without Disruption', 'fa-right-left', 'With SAP ending mainstream maintenance for ECC in 2027, migrating to SAP S/4HANA is no longer optional — it is a business imperative. Our Migration & Upgrade practice helps organisations transition safely, efficiently, and with minimal disruption to day-to-day operations.', 'Whether you are moving from SAP ECC to S/4HANA, upgrading to a newer release, or shifting from on-premise to cloud, our structured methodology covers every dimension — technical, functional, and organisational — to protect your data integrity and business continuity throughout.

Our consultants have delivered 150+ successful migrations across industries, using proven tools, accelerators, and a risk-first approach that keeps your business running throughout the transition.', 'Supports greenfield, brownfield, and selective data transition approaches
Pre-migration readiness assessment covering data, custom code, and integrations
Automated custom code scanning and remediation for S/4HANA compatibility
Near-zero downtime migration techniques minimising business disruption
Cloud migration support across AWS, Azure, and Google Cloud
Structured data cleansing, transformation, and validation cycles
Dedicated post-migration hypercare and stabilisation support
ECC maintenance deadline advisory and migration roadmap planning', 'linear-gradient(135deg, #0B356D 0%, #3b82f6 100%)', '30', NULL),
('8', '2', 'Digital Innovation', 'sap-innovation', 'Pioneering cutting-edge capabilities across core corporate processes.', 'fa-lightbulb', 'Local Global Services (LGS) delivers high-impact Digital Innovation capability to help enterprises design, build, and support their core operational layers. Backed by twenty years of global consulting experience, we ensure seamless system integrations, automated data synchronizations, and robust compliance controls.', 'Our dedicated team of 500+ certified specialists is focused on driving tangible business outcomes, lower TCO, and unmatched agility. By aligning Digital Innovation with your core business objectives, we deliver optimized operational performance, zero downtime conversions, and maximum ROI.', 'Tailored Industry Blueprints
Global Delivery Excellence
Continuous Support & Training
24/7 Monitoring & Optimization', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '35', NULL),
('9', '2', 'Cloud Transformation', 'sap-cloud', 'Accelerate Your Journey to the Cloud', 'fa-cloud', 'Cloud Transformation is more than a technology shift — it is a fundamental reimagining of how your business operates, innovates, and competes. Moving your SAP landscape to the cloud unlocks greater agility, scalability, and cost efficiency while positioning your organisation to adopt emerging technologies faster.', 'Our Cloud Transformation practice guides organisations through every stage of the journey — from cloud strategy and business case development to architecture design, migration execution, and post-migration optimisation — ensuring a smooth, secure, and value-driven transition.

With deep expertise across SAP, hyperscalers, and hybrid environments, we help you choose the right cloud model and build a future-ready digital foundation.', 'Comprehensive cloud strategy and business case development
Hyperscaler advisory across AWS, Azure, and Google Cloud
Support for public, private, hybrid, and multi-cloud deployments
SAP landscape assessment and cloud readiness evaluation
Infrastructure design, network architecture, and security planning
Seamless integration with existing on-premise and third-party systems
Cost optimisation through right-sizing and consumption-based pricing
Ongoing cloud governance, compliance, and performance management
Alignment with RISE with SAP and SAP BTP cloud strategy', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '40', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243196/localglobals/uploads/maekagag4hsrbu3kkiwu.png'),
('10', '2', 'SAP Business Technology Platform', 'sap-btp', 'Build, Integrate, and Extend with Confidence', 'fa-microchip', 'SAP Business Technology Platform (SAP BTP) is SAP''s unified platform for application development, integration, data management, and intelligent technologies. It empowers organisations to extend their SAP core, integrate disparate systems, and build innovative applications — all without compromising the clean-core principles essential for future upgrades.', 'Whether you need to automate complex business processes, connect SAP with third-party applications, or build custom extensions tailored to your unique requirements, SAP BTP provides the tools, services, and runtime environments to deliver quickly and confidently.

Our certified BTP consultants help you unlock the full potential of the platform — from initial strategy and architecture through to development, deployment, and ongoing management.', 'Extend SAP S/4HANA without modifying the core
Low-code and pro-code development using SAP Build and CAP framework
Pre-built integration content accelerating connectivity with third-party systems
SAP Integration Suite for seamless API and event-driven integrations
SAP Analytics Cloud for enterprise-wide reporting and planning
Robotic Process Automation (RPA) to eliminate manual, repetitive tasks
AI and machine learning services embedded across business processes
Multi-cloud deployment across AWS, Azure, and Google Cloud
Governance and security controls ensuring compliance and data protection', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '45', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243198/localglobals/uploads/kamnwcv28nu274lbs9cv.jpg'),
('11', '2', 'SAP Cloud Services', 'sap-cloud-services', 'SaaS business application suites designed for rapid scaling and global reach.', 'fa-cloud-sun', 'Local Global Services (LGS) delivers high-impact SAP Cloud Services capability to help enterprises design, build, and support their core operational layers. Backed by twenty years of global consulting experience, we ensure seamless system integrations, automated data synchronizations, and robust compliance controls.', 'Our dedicated team of 500+ certified specialists is focused on driving tangible business outcomes, lower TCO, and unmatched agility. By aligning SAP Cloud Services with your core business objectives, we deliver optimized operational performance, zero downtime conversions, and maximum ROI.', 'Tailored Industry Blueprints
Global Delivery Excellence
Continuous Support & Training
24/7 Monitoring & Optimization', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '50', NULL),
('12', '2', 'SAP S/4HANA Cloud', 's4-hana-cloud', 'Intelligent ERP Delivered from the Cloud', 'fa-server', 'SAP S/4HANA Cloud is the cloud-native version of SAP''s flagship ERP, delivering the full power of S/4HANA through a secure, scalable, and continuously updated cloud environment. It eliminates the burden of infrastructure management while ensuring your business always runs on the latest SAP innovations.', 'Available in two editions — Public Cloud for standardised, rapid deployments and Private Cloud for organisations requiring greater configurability — SAP S/4HANA Cloud gives businesses of all sizes a flexible path to intelligent, real-time ERP without the complexity of traditional on-premise landscapes.

Our consultants help you select the right edition, design your cloud architecture, and deliver a structured implementation that maximises adoption and minimises risk.', 'Two editions — Public Cloud for speed, Private Cloud for flexibility
Continuous quarterly updates with zero manual upgrade effort
Real-time analytics and reporting powered by HANA in-memory database
Embedded AI, machine learning, and intelligent automation capabilities
Rapid deployment using SAP''s preconfigured best-practice processes
Seamless integration with SAP and third-party applications via SAP BTP
Enterprise-grade security, data residency, and compliance controls
Reduced total cost of ownership compared to on-premise deployments
Scalable architecture that grows alongside your business needs', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '55', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243200/localglobals/uploads/bxy0f6obqdz8umsvsbvd.jpg'),
('13', '2', 'SAP SuccessFactors', 'sap-successfactors', 'Transform HR and Unlock the Power of Your People', 'fa-people-group', 'SAP SuccessFactors is SAP''s leading cloud-based Human Experience Management (HXM) suite, designed to help organisations attract, develop, engage, and retain talent in an increasingly competitive landscape. It goes beyond traditional HR by placing employee experience at the centre of every people process.', 'From core HR and payroll to recruitment, learning, performance management, and workforce analytics, SuccessFactors delivers a unified, intelligent HR platform that empowers both HR teams and employees — accessible anytime, anywhere, on any device.

Our certified SuccessFactors consultants bring deep functional expertise and a people-first implementation approach, ensuring your HR transformation delivers measurable business outcomes and a superior employee experience from day one.', 'Comprehensive HXM suite covering the full employee lifecycle
Core HR and payroll management across multiple countries and legislations
Intelligent recruitment and onboarding to attract and retain top talent
Continuous performance management and goal alignment tools
Personalised learning and development programmes at scale
Succession planning and career development pathways
Real-time workforce analytics and people insights for data-driven decisions
Seamless integration with SAP S/4HANA and third-party systems
Mobile-first design delivering an exceptional employee experience
Regular cloud updates ensuring access to the latest HR innovations', 'linear-gradient(135deg, #0B356D 0%, #7c3aed 100%)', '60', NULL),
('14', '2', 'SAP Ariba', 'sap-ariba', 'Intelligent Procurement for a Connected Supply Chain', 'fa-truck-ramp-box', 'SAP Ariba is the world''s leading cloud-based procurement and supply chain collaboration platform, connecting buyers and suppliers on the largest business network globally. It transforms procurement from a transactional function into a strategic driver of cost savings, supplier relationships, and supply chain resilience.', 'From sourcing and contract management to purchase orders, invoicing, and supplier risk management, SAP Ariba digitalises the entire source-to-pay process — delivering greater visibility, control, and compliance across every spend category.

Our SAP Ariba consultants bring deep procurement expertise and hands-on implementation experience, helping organisations streamline purchasing processes, reduce maverick spend, and build stronger, more transparent supplier partnerships.', 'End-to-end source-to-pay process digitalisation and automation
Strategic sourcing and e-auctions to drive competitive pricing
Centralised contract management with compliance monitoring
Supplier onboarding, qualification, and performance management
Real-time spend visibility and analytics across all categories
Automated invoice processing reducing manual effort and errors
Supplier risk management and supply chain resilience tools
Access to the world''s largest business commerce network
Seamless integration with SAP S/4HANA and third-party ERP systems
Cloud-based deployment with rapid implementation and low maintenance', 'linear-gradient(135deg, #0B356D 0%, #db2777 100%)', '65', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243203/localglobals/uploads/yzrylnexiadaqvcst7fk.jpg'),
('15', '2', 'Business Analytics', 'sap-analytics', 'Transform raw data silos into visual intelligence.', 'fa-chart-pie', 'Modern enterprises generate massive volumes of data, but without structured analysis, it remains dark. Business Analytics utilizing Microsoft Power BI, SAP Analytics Cloud, and advanced warehouse layers allows you to visualize trends, forecast demand, and make data-driven decisions.', 'LGS builds enterprise business intelligence systems. We connect disparate CRM, ERP, and IoT data feeds, construct automated ETL pipelines, and create stunning interactive dashboards that allow executives to inspect details from high-level summaries.', 'Multi-source ETL Data Pipelines
Stunning Visual BI Dashboards
Predictive Machine Learning Modeling
Mobile & Scheduled Automated Reports', 'linear-gradient(135deg, #0B356D 0%, #b45309 100%)', '70', NULL),
('16', '2', 'SAP Analytics', 'sap-analytics-cloud', 'Turn Data into Decisions with Confidence', 'fa-circle-nodes', 'SAP Analytics empowers organisations to move beyond traditional reporting and embrace a data-driven culture where every decision — from boardroom strategy to frontline operations — is informed by accurate, real-time insights.', 'Built around SAP Analytics Cloud (SAC) as its centrepiece, SAP''s analytics portfolio combines business intelligence, planning, and predictive capabilities in a single, unified platform. Integrated natively with SAP S/4HANA and BTP, it eliminates data silos and delivers a single source of truth across finance, operations, sales, and HR.

Our analytics consultants help you design, build, and embed analytics across your organisation — from data architecture and modelling to dashboard development, financial planning, and self-service reporting — enabling faster, smarter, and more confident decision-making at every level.', 'Unified platform combining BI, planning, and predictive analytics
Real-time reporting and dashboards powered by SAP HANA in-memory database
Integrated financial planning, budgeting, and forecasting capabilities
Predictive analytics and machine learning for forward-looking insights
Self-service reporting empowering business users without IT dependency
Native integration with SAP S/4HANA, BTP, and third-party data sources
Mobile-first design for analytics access anytime, anywhere
Embedded analytics within SAP transactions for in-context decision making
Enterprise-wide data governance and security controls
Scalable architecture supporting organisation-wide analytics adoption', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '75', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243205/localglobals/uploads/garo1uqeatfxqszp66br.jpg'),
('17', '2', 'Power BI', 'power-bi', 'Visualise, Analyse, and Act on Your Data', 'fa-chart-bar', 'Power BI is Microsoft''s industry-leading business intelligence and data visualisation platform, enabling organisations to connect, transform, and visualise data from virtually any source — delivering rich, interactive dashboards and reports that drive faster, more informed decision-making across every business function.', 'Whether you need executive scorecards, operational dashboards, or self-service analytics for business users, Power BI provides an intuitive, scalable, and cost-effective solution that integrates seamlessly with your existing Microsoft ecosystem and SAP landscape.

Our Power BI consultants combine deep data expertise with strong business acumen — helping you design meaningful reports, build robust data models, and embed analytics directly into the workflows and tools your people use every day.', 'Interactive dashboards and reports with rich data visualisation capabilities
Seamless connectivity with SAP S/4HANA, Azure, Excel, and 100+ data sources
Self-service analytics empowering business users to explore data independently
Power BI Embedded for integrating analytics into custom applications
Real-time data streaming and live dashboard refresh capabilities
Robust data modelling using DAX and Power Query for complex calculations
Enterprise-grade security with row-level access and data governance controls
Mobile-optimised reports accessible anytime on any device
Seamless integration with Microsoft Teams, SharePoint, and Office 365
Cost-effective licensing within the Microsoft 365 ecosystem', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '80', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243208/localglobals/uploads/l2wyddlwsjajubg1bprm.png'),
('18', '2', 'SAP Fiori Enablement', 'fiori-enablement', 'Delivering Exceptional User Experiences Across SAP', 'fa-laptop', 'SAP Fiori is SAP''s modern user experience (UX) design framework, transforming how users interact with SAP applications through intuitive, role-based, and responsive interfaces. It replaces complex, traditional SAP transactions with clean, consumer-grade applications that work seamlessly across desktop, tablet, and mobile devices.', 'Fiori enablement is no longer optional — it is central to driving user adoption, productivity, and satisfaction in any SAP S/4HANA or cloud deployment. A well-designed Fiori experience reduces training time, minimises errors, and empowers employees to complete tasks faster and with greater confidence.

Our Fiori consultants combine deep UX expertise with strong SAP functional knowledge — delivering tailored Fiori implementations that align with your business processes, brand identity, and user needs.', 'Role-based Fiori app configuration tailored to business user needs
Responsive design delivering consistent experience across all devices
Custom Fiori app development using SAP UI5 and CAP framework
Fiori Launchpad design, configuration, and personalisation
Integration with SAP S/4HANA, BTP, and third-party systems
UX discovery workshops to map user journeys and pain points
Accessibility compliance ensuring inclusive design for all users
Performance optimisation for fast, seamless application load times
Change management and user training to maximise adoption
Ongoing Fiori landscape support and continuous UX improvement', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '85', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243211/localglobals/uploads/rdj6oqhdqnxidgmnxqew.jpg'),
('19', '2', 'Specialized SAP Module Services', 'sap-specialized-modules', 'Deep vertical functional expertise across niche core SAP ERP modules.', 'fa-users-gear', 'Local Global Services (LGS) delivers high-impact Specialized SAP Module Services capability to help enterprises design, build, and support their core operational layers. Backed by twenty years of global consulting experience, we ensure seamless system integrations, automated data synchronizations, and robust compliance controls.', 'Our dedicated team of 500+ certified specialists is focused on driving tangible business outcomes, lower TCO, and unmatched agility. By aligning Specialized SAP Module Services with your core business objectives, we deliver optimized operational performance, zero downtime conversions, and maximum ROI.', 'Tailored Industry Blueprints
Global Delivery Excellence
Continuous Support & Training
24/7 Monitoring & Optimization', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '90', NULL),
('20', '2', 'SAP HCM Implementation', 'sap-hcm', 'Building a Strong Foundation for Your Workforce', 'fa-user-tie', 'SAP Human Capital Management (HCM) is SAP''s comprehensive on-premise HR solution, enabling organisations to manage every aspect of the employee lifecycle — from recruitment and onboarding to payroll, time management, organisational management, and talent development — within a single, integrated platform.', 'For organisations with complex HR requirements, multi-country payroll needs, or deep integration dependencies with existing SAP landscapes, SAP HCM remains a robust and highly configurable solution trusted by thousands of enterprises globally.

Our certified SAP HCM consultants bring extensive functional and technical expertise across all HCM modules, delivering implementations that are accurate, compliant, and aligned to your unique organisational structure and HR policies.', 'End-to-end implementation across all core SAP HCM modules
Organisational management and workforce structure configuration
Local and multi-country payroll implementation and compliance
Time and attendance management including complex shift patterns
Personnel administration and employee master data management
Recruitment, onboarding, and talent management configuration
Training and event management for workforce development
Deep integration with SAP S/4HANA Finance and third-party systems
Custom reporting and HR analytics using SAP HCM tools
Post-implementation support, payroll audits, and continuous optimisation', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '95', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243214/localglobals/uploads/eclxwjod7zgfqmwfubkn.jpg'),
('21', '2', 'ReFx Implementation', 'sap-refx', 'SAP Flexible Real Estate Management to streamline lease contracts, asset tracking, and property accounting.', 'fa-house-laptop', 'Local Global Services (LGS) delivers high-impact ReFx Implementation capability to help enterprises design, build, and support their core operational layers. Backed by twenty years of global consulting experience, we ensure seamless system integrations, automated data synchronizations, and robust compliance controls.', 'Our dedicated team of 500+ certified specialists is focused on driving tangible business outcomes, lower TCO, and unmatched agility. By aligning ReFx Implementation with your core business objectives, we deliver optimized operational performance, zero downtime conversions, and maximum ROI.', 'Tailored Industry Blueprints
Global Delivery Excellence
Continuous Support & Training
24/7 Monitoring & Optimization', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '100', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243217/localglobals/uploads/yr8ahvh8lawbdyfy0bzy.jpg'),
('22', '2', 'SAP Document Management System', 'sap-dms', 'Centralise, Control, and Streamline Your Documents', 'fa-file-shield', 'SAP Document Management System (DMS) is SAP''s enterprise-grade solution for managing the entire lifecycle of business documents — from creation and storage to versioning, distribution, and archiving — directly within your SAP environment. It eliminates the inefficiencies of fragmented document storage and ensures the right information is always accessible to the right people at the right time.', 'By integrating documents directly with SAP business objects such as materials, equipment, purchase orders, and projects, SAP DMS creates a seamless connection between your documents and the business processes they support — improving accuracy, compliance, and operational efficiency across the organisation.

Our SAP DMS consultants help you design, configure, and deploy a document management framework tailored to your industry requirements and business workflows.', 'Centralised document storage with controlled access and permissions
Full document lifecycle management from creation to archiving
Version control ensuring teams always work on the latest document
Direct integration with SAP business objects and transactions
Classification and metadata management for fast document retrieval
Workflow automation for document approval and review processes
Support for all document formats including CAD, PDF, and Office files
Compliance with regulatory and audit requirements for document retention
Integration with SAP S/4HANA, MM, PM, PP, and Project Systems
Reduced paper-based processes driving operational efficiency and cost savings', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '105', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243218/localglobals/uploads/s70mg6tg3zcq9cxpkt9f.jpg'),
('23', '2', 'SAP Environment, Health & Safety Management', 'sap-ehs', 'Building a Safer, More Sustainable Workplace', 'fa-shield-heart', 'SAP Environment, Health & Safety (EHS) Management is SAP''s integrated solution for managing workplace safety, environmental compliance, and sustainability programmes across your organisation. It helps businesses proactively identify risks, prevent incidents, meet regulatory obligations, and build a culture of safety and environmental responsibility.', 'In an era of increasing regulatory scrutiny and growing stakeholder expectations around ESG performance, SAP EHS provides the tools and visibility needed to manage compliance confidently, reduce workplace incidents, and demonstrate measurable progress towards your sustainability goals.

Our certified SAP EHS consultants bring deep industry knowledge and functional expertise — delivering implementations that embed safety and environmental management directly into your core SAP business processes.', 'Incident management and near-miss reporting with root cause analysis
Risk assessment and hazard identification across all workplace activities
Regulatory compliance management for local and international EHS standards
Chemical and hazardous substance management with safety data sheets
Environmental monitoring and emissions tracking for sustainability reporting
Occupational health management including medical surveillance and fitness tracking
Permit-to-work and contractor safety management workflows
Audit and inspection management with corrective action tracking
Seamless integration with SAP S/4HANA, PM, and HR modules
Real-time EHS dashboards and analytics for proactive decision making
ESG reporting support aligned with GRI, CDP, and sustainability frameworks', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '110', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243221/localglobals/uploads/f0zcd5gghmz73nm60iec.jpg'),
('24', '2', 'OpenText', 'sap-opentext', 'Intelligent Information Management Integrated with SAP', 'fa-folder-tree', 'OpenText is the world''s leading Enterprise Information Management (EIM) platform, enabling organisations to capture, manage, and process business content and documents seamlessly within their SAP environment. By integrating OpenText directly with SAP, businesses eliminate paper-based processes, accelerate document-driven workflows, and ensure critical business information is always connected to the right SAP transactions.', 'From accounts payable invoice processing and vendor invoice management to extended ECM for SAP and archiving solutions, OpenText transforms how organisations handle unstructured content — reducing manual effort, improving compliance, and delivering significant cost savings across business functions.

Our OpenText and SAP certified consultants help you design and implement a tailored information management strategy that connects people, processes, and content across your entire SAP landscape.', 'Seamless integration of OpenText ECM directly within SAP transactions
Automated capture and processing of incoming invoices and documents
Vendor Invoice Management (VIM) for streamlined accounts payable processes
Extended ECM connecting documents to SAP business objects and workflows
Intelligent data extraction using OCR and AI-powered document recognition
Digital archiving and records management ensuring regulatory compliance
Automated document routing, approval workflows, and audit trails
Secure, centralised content repository with role-based access controls
Support for SAP S/4HANA, MM, FI, SD, HR, and Project Systems
Significant reduction in manual data entry errors and processing costs
Compliance with GDPR, legal hold, and long-term document retention requirements', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '115', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243223/localglobals/uploads/kc7nwnpywpcyd1l2zg1x.jpg'),
('25', '2', 'Resource Augmentation', 'resource-augmentation', 'The Right SAP Expertise, When You Need It', 'fa-user-plus', 'Resource Augmentation provides organisations with immediate access to skilled, certified SAP professionals who seamlessly integrate into your existing teams — bridging capability gaps, accelerating project delivery, and ensuring your SAP programmes are supported by the right expertise at every stage.', 'Whether you need to supplement your internal team during a major implementation, cover a critical skills shortage, or scale resources rapidly to meet project demands, our resource augmentation model gives you the flexibility and speed to respond without the cost and commitment of permanent hiring.

Our extensive network of pre-vetted SAP consultants spans all modules, technologies, and industries — enabling us to place the right resource quickly, with minimal onboarding time and maximum impact from day one.', 'Access to a deep bench of certified SAP consultants across all modules
Flexible engagement models — short-term, long-term, or project-based
Rapid mobilisation with resources available at short notice
Pre-vetted professionals with proven track records and client references
Coverage across functional, technical, and basis SAP skill sets
Seamless integration into your existing project teams and ways of working
Support for implementations, rollouts, upgrades, and BAU operations
On-site, remote, or hybrid working arrangements to suit your needs
Dedicated account management ensuring quality and performance oversight
Cost-effective alternative to permanent hiring for specialist SAP skills', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '120', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243230/localglobals/uploads/fysq7snlwfogu7jibrv4.jpg'),
('26', '2', 'Internet Of Things (IOT)', 'iot-core', 'Connecting physical enterprise assets to intelligent cloud telemetry networks.', 'fa-wifi', 'Local Global Services (LGS) delivers high-impact Internet Of Things (IOT) capability to help enterprises design, build, and support their core operational layers. Backed by twenty years of global consulting experience, we ensure seamless system integrations, automated data synchronizations, and robust compliance controls.', 'Our dedicated team of 500+ certified specialists is focused on driving tangible business outcomes, lower TCO, and unmatched agility. By aligning Internet Of Things (IOT) with your core business objectives, we deliver optimized operational performance, zero downtime conversions, and maximum ROI.', 'Tailored Industry Blueprints
Global Delivery Excellence
Continuous Support & Training
24/7 Monitoring & Optimization', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '125', NULL),
('27', '2', 'Production Track', 'iot-production', 'Connecting factory hardware to the cloud for real-time OEE visibility.', 'fa-industry', 'Internet of Things (IoT) in manufacturing bridges physical hardware with cloud databases. By mounting smart sensors, PLC collectors, and edge computers on the assembly lines, managers can track machine uptime, count products, and measure Overall Equipment Effectiveness (OEE) in real-time.', 'LGS constructs end-to-end industrial IoT solutions. We install ruggedized hardware collectors, deploy cloud ingest brokers, and compile real-time telemetry into modern shop-floor dashboards, allowing engineers to prevent bottlenecks instantly.', 'Real-Time Shopfloor OEE Calculations
Predictive Vibration & Heat Monitors
Automated PLC Production Counters
Secure Low-Latency Industrial Networks', 'linear-gradient(135deg, #0B356D 0%, #065f46 100%)', '130', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243232/localglobals/uploads/xpqtsbptrqupbucccvu6.jpg'),
('28', '2', 'Vehicle Live Tracking System', 'iot-vehicle', 'Real-time GPS fleet telemetry and logistics optimization.', 'fa-truck-fast', 'Fleet logistics operations require continuous coordination. A Vehicle Live Tracking System combines rugged GPS trackers, cellular transceivers, and specialized web consoles to plot vehicle location, speed, fuel utilization, and sensor values dynamically.', 'Our LGS IoT team deploys enterprise-grade fleet tracking systems. We supply cellular tracking devices, integrate OBD-II telemetry, and build intelligent routing engines that help distribution centers lower fuel costs and ensure secure deliveries.', 'Real-Time GPS & Geofence Alerts
OBD-II Telemetry Diagnostics
Traffic-Aware Dispatch Routing
Driver Safety Scoring Sheets', 'linear-gradient(135deg, #0B356D 0%, #0369a1 100%)', '135', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243234/localglobals/uploads/jue8iaxelxi4b6tdu8oc.jpg'),
('29', '2', 'Fuel Consuption Monitoring System', 'iot-fuel', 'Preventing logistics leakages through high-accuracy fuel telemetry.', 'fa-gas-pump', 'Local Global Services (LGS) delivers high-impact Fuel Consuption Monitoring System capability to help enterprises design, build, and support their core operational layers. Backed by twenty years of global consulting experience, we ensure seamless system integrations, automated data synchronizations, and robust compliance controls.', 'Our dedicated team of 500+ certified specialists is focused on driving tangible business outcomes, lower TCO, and unmatched agility. By aligning Fuel Consuption Monitoring System with your core business objectives, we deliver optimized operational performance, zero downtime conversions, and maximum ROI.', 'Tailored Industry Blueprints
Global Delivery Excellence
Continuous Support & Training
24/7 Monitoring & Optimization', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '140', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243235/localglobals/uploads/jirxpoc9r8lbkxhc7fdt.jpg'),
('30', '2', 'Bus Tracking System', 'iot-bus', 'Ensuring workforce transit safety through real-time transit telemetry and scheduling engines.', 'fa-bus', 'Local Global Services (LGS) delivers high-impact Bus Tracking System capability to help enterprises design, build, and support their core operational layers. Backed by twenty years of global consulting experience, we ensure seamless system integrations, automated data synchronizations, and robust compliance controls.', 'Our dedicated team of 500+ certified specialists is focused on driving tangible business outcomes, lower TCO, and unmatched agility. By aligning Bus Tracking System with your core business objectives, we deliver optimized operational performance, zero downtime conversions, and maximum ROI.', 'Tailored Industry Blueprints
Global Delivery Excellence
Continuous Support & Training
24/7 Monitoring & Optimization', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '145', NULL),
('31', '2', 'Temperature Monitoring', 'iot-temp', 'Continuous cold-chain visual audits for pharmaceutical and food logistics compliance.', 'fa-temperature-quarter', 'Local Global Services (LGS) delivers high-impact Temperature Monitoring capability to help enterprises design, build, and support their core operational layers. Backed by twenty years of global consulting experience, we ensure seamless system integrations, automated data synchronizations, and robust compliance controls.', 'Our dedicated team of 500+ certified specialists is focused on driving tangible business outcomes, lower TCO, and unmatched agility. By aligning Temperature Monitoring with your core business objectives, we deliver optimized operational performance, zero downtime conversions, and maximum ROI.', 'Tailored Industry Blueprints
Global Delivery Excellence
Continuous Support & Training
24/7 Monitoring & Optimization', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '150', NULL),
('32', '2', 'Heavy Equipment Tracking', 'iot-equipment', 'Ruggedized tracking, fuel monitors, and run-hour logs for massive off-road excavators.', 'fa-tractor', 'Local Global Services (LGS) delivers high-impact Heavy Equipment Tracking capability to help enterprises design, build, and support their core operational layers. Backed by twenty years of global consulting experience, we ensure seamless system integrations, automated data synchronizations, and robust compliance controls.', 'Our dedicated team of 500+ certified specialists is focused on driving tangible business outcomes, lower TCO, and unmatched agility. By aligning Heavy Equipment Tracking with your core business objectives, we deliver optimized operational performance, zero downtime conversions, and maximum ROI.', 'Tailored Industry Blueprints
Global Delivery Excellence
Continuous Support & Training
24/7 Monitoring & Optimization', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '155', NULL),
('33', '2', 'RFID Solutions', 'iot-rfid', 'Automated warehouse tracking utilizing smart gate receivers and RFID bulk inventory auditing.', 'fa-barcode', 'Local Global Services (LGS) delivers high-impact RFID Solutions capability to help enterprises design, build, and support their core operational layers. Backed by twenty years of global consulting experience, we ensure seamless system integrations, automated data synchronizations, and robust compliance controls.', 'Our dedicated team of 500+ certified specialists is focused on driving tangible business outcomes, lower TCO, and unmatched agility. By aligning RFID Solutions with your core business objectives, we deliver optimized operational performance, zero downtime conversions, and maximum ROI.', 'Tailored Industry Blueprints
Global Delivery Excellence
Continuous Support & Training
24/7 Monitoring & Optimization', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '160', NULL),
('34', '2', 'Application Development', 'app-dev-core', 'Bespoke corporate web apps and core custom workflow architectures.', 'fa-code', 'Local Global Services (LGS) delivers high-impact Application Development capability to help enterprises design, build, and support their core operational layers. Backed by twenty years of global consulting experience, we ensure seamless system integrations, automated data synchronizations, and robust compliance controls.', 'Our dedicated team of 500+ certified specialists is focused on driving tangible business outcomes, lower TCO, and unmatched agility. By aligning Application Development with your core business objectives, we deliver optimized operational performance, zero downtime conversions, and maximum ROI.', 'Tailored Industry Blueprints
Global Delivery Excellence
Continuous Support & Training
24/7 Monitoring & Optimization', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '165', NULL),
('35', '2', 'Application Development: Vendor Portal', 'app-vendor-portal', 'Streamline Supplier Collaboration and Engagement', 'fa-laptop-code', 'A purpose-built Vendor Portal transforms how your organisation engages with suppliers — replacing fragmented email chains, manual processes, and disconnected spreadsheets with a single, secure, and intuitive digital platform. It empowers vendors to self-serve, collaborate in real time, and transact efficiently while giving your procurement and finance teams complete visibility and control.', 'Built on SAP S/4HANA, our custom Vendor Portal solutions are designed around your specific procurement workflows, supplier onboarding requirements, and business rules — delivering a tailored experience that drives adoption, reduces processing time, and strengthens supplier relationships.

Our application development team combines deep SAP technical expertise with modern UX design principles to deliver a portal that is intuitive for suppliers and powerful for your internal teams.', 'Self-service supplier registration, onboarding, and profile management
Real-time purchase order visibility, confirmation, and tracking
Automated invoice submission, status tracking, and payment visibility
Document exchange including contracts, certificates, and compliance documents
Supplier performance scorecards and KPI dashboards
Integrated workflows for approvals, queries, and dispute resolution
Role-based access controls ensuring data security and confidentiality
Mobile-responsive design accessible on any device
Configurable notifications and alerts keeping suppliers informed at every stage
Scalable architecture supporting hundreds of concurrent supplier users', 'linear-gradient(135deg, #0B356D 0%, #5b21b6 100%)', '170', NULL),
('36', '2', 'Application Development: Weighbridge Integration', 'app-weighbridge', 'Automate Weight Capture and Eliminate Manual Errors', 'fa-scale-balanced', 'Weighbridge Integration connects your physical weighbridge infrastructure directly with SAP — automating the capture, validation, and processing of weight data in real time and eliminating the manual data entry, transcription errors, and delays that plague paper-based weighbridge operations.', 'Whether you operate in logistics, mining, agriculture, manufacturing, or waste management, accurate weight measurement is critical to inventory management, billing, compliance, and supplier settlements. Our custom Weighbridge Integration solution ensures every transaction is captured accurately, processed instantly, and fully traceable within your SAP landscape.

integrated with SAP S/4HANA, our solution bridges the gap between operational technology and enterprise systems — delivering end-to-end automation from weighbridge to SAP transaction with minimal human intervention.', 'Real-time, automated weight data capture directly from weighbridge hardware
Seamless integration with SAP S/4HANA, MM, SD, and WM modules
Elimination of manual data entry reducing errors and processing time
Automated goods receipt, goods issue, and delivery note creation in SAP
Support for single and double weighing — tare, gross, and net weight calculations
Vehicle and driver identification via RFID, barcode, or licence plate recognition
Automated alerts for weight discrepancies, overloads, and compliance breaches
Full audit trail of all weighbridge transactions within SAP
Real-time dashboards for weighbridge operations and throughput monitoring
Integration with billing, invoicing, and supplier settlement processes
Scalable solution supporting single or multi-site weighbridge operations', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '175', NULL),
('37', '2', 'Application Development: Digital Signature Integration', 'sol-digisign', 'Accelerate Approvals and Eliminate Paper-Based Signing', 'fa-signature', 'Digital Signature Integration embeds legally binding electronic signature capabilities directly into your SAP workflows — transforming slow, paper-dependent approval processes into fast, secure, and fully auditable digital transactions. It eliminates the inefficiencies of printing, signing, scanning, and filing documents while ensuring every signature is authenticated, timestamped, and tamper-proof.', 'Whether applied to purchase orders, contracts, goods receipts, quality certificates, HR documents, or financial approvals, our Digital Signature Integration solution seamlessly connects leading e-signature platforms with SAP S/4HANA — enabling approvals to happen in minutes rather than days, from any device, anywhere in the world.

Our development team designs and implements a tailored integration architecture that aligns with your document workflows, approval hierarchies, and regulatory compliance requirements.', 'Legally binding electronic signatures compliant with eIDAS, ESIGN, and local regulations
Native embedding within SAP S/4HANA workflows and document processes
Automated signature routing based on approval hierarchies and business rules
Real-time signature status tracking and automated reminders for pending approvals
Tamper-proof audit trail capturing signer identity, timestamp, and IP address
Support for single, sequential, and parallel multi-party signing workflows
Applicable across Purchase Orders, Contracts, HR Documents, and Financial Approvals
Mobile-responsive signing experience accessible on any device anywhere
Secure document storage with full version control and retrieval within SAP
Significant reduction in approval cycle times and document processing costs', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '180', NULL),
('38', '2', 'X-Lease Management Solution', 'app-xlease', 'Dedicated high-performing corporate real estate lease and asset amortization dashboard.', 'fa-building-shield', 'Local Global Services (LGS) delivers high-impact X-Lease Management Solution capability to help enterprises design, build, and support their core operational layers. Backed by twenty years of global consulting experience, we ensure seamless system integrations, automated data synchronizations, and robust compliance controls.', 'Our dedicated team of 500+ certified specialists is focused on driving tangible business outcomes, lower TCO, and unmatched agility. By aligning X-Lease Management Solution with your core business objectives, we deliver optimized operational performance, zero downtime conversions, and maximum ROI.', 'Tailored Industry Blueprints
Global Delivery Excellence
Continuous Support & Training
24/7 Monitoring & Optimization', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '185', NULL),
('39', '2', 'Tender Process', 'app-tender', 'Secure online portal to manage B2B procurement bids, technical evaluations, and selections.', 'fa-file-contract', 'Local Global Services (LGS) delivers high-impact Tender Process capability to help enterprises design, build, and support their core operational layers. Backed by twenty years of global consulting experience, we ensure seamless system integrations, automated data synchronizations, and robust compliance controls.', 'Our dedicated team of 500+ certified specialists is focused on driving tangible business outcomes, lower TCO, and unmatched agility. By aligning Tender Process with your core business objectives, we deliver optimized operational performance, zero downtime conversions, and maximum ROI.', 'Tailored Industry Blueprints
Global Delivery Excellence
Continuous Support & Training
24/7 Monitoring & Optimization', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '190', NULL),
('40', '2', 'Hospital Management', 'app-hims', 'HIMS: Transforming Healthcare Delivery Through Intelligent Technology', 'fa-hospital', 'A Hospital Information Management System (HIMS) is the digital backbone of a modern healthcare organisation — integrating clinical, administrative, financial, and operational processes into a single, unified platform that enables hospitals and healthcare providers to deliver superior patient care, streamline operations, and maintain regulatory compliance.', 'Our HIMS solution is purpose-built for hospitals, multi-specialty clinics, diagnostic centres, and healthcare networks — combining deep healthcare domain expertise with cutting-edge technology to digitise every aspect of hospital operations. From patient registration and outpatient management to inpatient care, surgery scheduling, pharmacy, laboratory, radiology, and billing, our HIMS delivers a seamless, end-to-end digital experience for patients, clinicians, and administrators alike.

Built on a modern, scalable architecture and integrated with SAP for finance and HR management, our HIMS solution empowers healthcare organisations to improve clinical outcomes, reduce operational costs, and deliver an exceptional patient experience at every touchpoint.', 'Patient registration, appointment scheduling, and outpatient department management
Inpatient management covering admission, bed allocation, ward management, and discharge
Electronic Medical Records (EMR) providing complete patient history and clinical documentation
Operation theatre scheduling, surgical workflow management, and anaesthesia records
Pharmacy management including drug dispensing, inventory control, and expiry tracking
Laboratory information system for test ordering, sample tracking, and result reporting
Radiology information system integrated with PACS for imaging and diagnostic management
Nursing station management with real-time patient monitoring and care plan documentation
Integrated billing, insurance claims processing, and revenue cycle management
Diet and nutrition management aligned with clinical care plans and patient requirements
Inventory and medical supplies management across all hospital departments
Integration with SAP S/4HANA for finance, procurement, and human resource management
Real-time hospital performance dashboards and clinical analytics for management decision making
Compliance with healthcare regulations and data privacy standards including HIPAA and local requirements', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '195', NULL),
('41', '2', 'Artificial Intelligence', 'ai-core', 'Cognitive algorithms and robotic process automation driving hyper-efficiency.', 'fa-brain', 'Local Global Services (LGS) delivers high-impact Artificial Intelligence capability to help enterprises design, build, and support their core operational layers. Backed by twenty years of global consulting experience, we ensure seamless system integrations, automated data synchronizations, and robust compliance controls.', 'Our dedicated team of 500+ certified specialists is focused on driving tangible business outcomes, lower TCO, and unmatched agility. By aligning Artificial Intelligence with your core business objectives, we deliver optimized operational performance, zero downtime conversions, and maximum ROI.', 'Tailored Industry Blueprints
Global Delivery Excellence
Continuous Support & Training
24/7 Monitoring & Optimization', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '200', NULL),
('42', '2', 'SAP Auto Cheque Management', 'ai-cheque', 'Intelligent Cheque Processing Powered by AI and SAP Integration', 'fa-money-bill-transfer', 'SAP Auto Cheque Management revolutionises how organisations process incoming cheques by combining AI-powered data extraction, scanner integration, and seamless SAP posting into a single, fully automated workflow. It eliminates the time-consuming, error-prone manual process of reading, recording, and posting cheque details — replacing it with an intelligent, touchless system that captures cheque data instantly, validates it automatically, and posts it directly into SAP Finance in real time.', 'Whether processing high volumes of customer cheques, vendor refunds, or inter-company payments, our solution dramatically accelerates cheque processing cycles, eliminates data entry errors, and gives your finance team complete visibility and control over every cheque transaction — from physical receipt to SAP posting and bank reconciliation.

Built on SAP BTP and integrated with SAP S/4HANA Finance, our Auto Cheque Management solution combines physical scanner connectivity, AI document intelligence, and SAP workflow automation — delivering a truly end-to-end, paperless cheque management process.', 'Physical cheque scanning via high-speed document scanners and multifunction devices
AI-powered optical character recognition (OCR) extracting cheque number, date, amount, payee, and bank details
Intelligent data validation cross-referencing extracted cheque data against SAP customer and vendor master records
Automated cheque posting directly into SAP S/4HANA FI accounts receivable and accounts payable
Exception handling workflows for unreadable, duplicate, or mismatched cheque data
Real-time cheque status tracking from physical receipt through to bank clearance in SAP
Automated cheque deposit scheduling and bank submission workflows
Bounce and dishonour cheque management with automated reversal and notification workflows
Integration with SAP Bank Communication Management for end-to-end bank reconciliation
Digital cheque image archiving within SAP DMS for audit trail and retrieval
Multi-bank, multi-currency, and multi-entity cheque processing support
Real-time cheque processing dashboards and finance analytics for management visibility', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '205', NULL),
('43', '2', 'Vendor/AP Invoice Automation', 'ai-invoice-automation', 'Eliminate Manual Processing and Accelerate Accounts Payable', 'fa-brain', 'Vendor and Accounts Payable Invoice Automation transform your invoice processing function by replacing manual data entry, paper-based workflows, and disconnected approval chains with an intelligent, end-to-end automated process — directly integrated with SAP. It captures invoices from any channel, extracts data using AI, validates against SAP purchase orders and contracts, routes for approval automatically, and posts to SAP Finance without human intervention.', 'Accounts payable teams managing high invoice volumes through manual processes face significant challenges — slow cycle times, data entry errors, duplicate payments, missed early payment discounts, and strained supplier relationships. Our AP Invoice Automation solution eliminates these pain points by embedding intelligent automation directly into your SAP environment — enabling your finance team to process more invoices faster, with greater accuracy and at significantly lower cost per invoice.

Built on SAP BTP and integrated with SAP S/4HANA, OpenText VIM, and leading AI document intelligence platforms, our solution handles the full invoice lifecycle from receipt to payment — across all invoice formats, channels, and geographies.', 'Multi-channel invoice capture including email, EDI, portal submission, and physical scanning
AI-powered OCR and machine learning for accurate extraction of invoice header and line item data
Automated validation and three-way matching against SAP purchase orders and goods receipts
Intelligent workflow routing for approval based on invoice type, value, and cost centre
Exception management and automated vendor communication for disputed or mismatched invoices
Duplicate invoice detection preventing overpayments and financial leakage
Early payment discount identification and automated capture for cost savings
Seamless integration with SAP S/4HANA FI, MM, and OpenText Vendor Invoice Management
Support for all invoice formats including PDF, XML, EDI, PEPPOL, and scanned paper invoices
Vendor self-service portal for invoice submission, status tracking, and query resolution
Real-time AP dashboards covering invoice volumes, cycle times, and aging analysis
Comprehensive audit trail supporting internal controls, SOX compliance, and external audits
Significant reduction in cost per invoice and accounts payable headcount requirements', 'linear-gradient(135deg, #0B356D 0%, #9d174d 100%)', '210', NULL),
('44', '2', 'Cloud', 'ai-cloud', 'Auto-scaling enterprise application hosting with cognitive load-balancing frameworks.', 'fa-cloud-arrow-up', 'Local Global Services (LGS) delivers high-impact Cloud capability to help enterprises design, build, and support their core operational layers. Backed by twenty years of global consulting experience, we ensure seamless system integrations, automated data synchronizations, and robust compliance controls.', 'Our dedicated team of 500+ certified specialists is focused on driving tangible business outcomes, lower TCO, and unmatched agility. By aligning Cloud with your core business objectives, we deliver optimized operational performance, zero downtime conversions, and maximum ROI.', 'Tailored Industry Blueprints
Global Delivery Excellence
Continuous Support & Training
24/7 Monitoring & Optimization', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '215', NULL),
('45', '2', 'Low Code Development', 'ai-lowcode', 'Build Faster, Innovate Smarter, Empower Your Business', 'fa-cubes', 'Low Code Development empowers organisations to build, extend, and automate business applications at speed — with minimal hand-written code and without placing additional burden on already stretched IT development teams. By leveraging visual development tools, pre-built components, and drag-and-drop interfaces, low code dramatically accelerates application delivery while enabling business users and citizen developers to actively participate in building the solutions they need.', 'In today''s fast-moving business environment, organisations cannot afford to wait months for custom application development. Our Low Code practice leverages SAP Build — SAP''s flagship low code platform — alongside other leading tools to rapidly deliver tailored business applications, process automations, and digital workflows that integrate seamlessly with your SAP landscape and third-party systems.

Our consultants combine low code expertise with deep SAP functional knowledge — ensuring every application we build is not only delivered quickly but is also robust, scalable, and aligned with your enterprise architecture and governance standards.', 'Rapid application development using SAP Build Apps, SAP Build Process Automation, and SAP Build Work Zone
Visual drag-and-drop development reducing time-to-delivery by up to 70%
Citizen developer enablement empowering business users to build their own solutions
Pre-built templates and components accelerating development across common business scenarios
Seamless integration with SAP S/4HANA, BTP, and third-party systems via APIs
Robotic Process Automation (RPA) for eliminating repetitive manual tasks and workflows
Custom workflow automation replacing email-based approval and notification processes
Mobile-first application design delivering intuitive experiences on any device
Enterprise-grade governance, security, and access controls across all low code applications
Scalable architecture ensuring low code applications grow alongside your business needs
Centre of Excellence setup and citizen developer training programmes for self-sufficiency
Significant reduction in custom development costs and application delivery timelines', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '220', NULL),
('46', '2', 'SAP Consulting', 'ai-consulting', 'Strategic Expertise to Maximise Your SAP Investment', 'fa-compass', 'SAP Consulting sits at the intersection of business strategy and technology delivery — helping organisations define their SAP roadmap, optimise existing implementations, and unlock the full potential of their SAP investment. Whether you are embarking on a new SAP journey, navigating a complex transformation, or seeking to extract greater value from your current landscape, our consulting practice provides the expertise, insight, and guidance to make it happen.', 'Our consultants bring decades of combined experience across industries and SAP solutions — working as trusted advisors who understand both the technical complexities of SAP and the business imperatives that drive transformation. We go beyond implementation to help you align SAP with your long-term business strategy, governance model, and operating model — ensuring every SAP investment delivers measurable, sustainable business value.

From initial assessment and business case development through to programme governance, process optimisation, and post-implementation review, our consulting-led approach ensures your SAP programme succeeds on every dimension — on time, on budget, and on value.', 'SAP landscape assessment and current-state analysis identifying improvement opportunities
SAP strategy and roadmap development aligned to long-term business objectives
Business case development and ROI modelling for SAP investment decisions
Programme and project governance ensuring structured, risk-managed delivery
Business process optimisation leveraging SAP best practices and industry benchmarks
Operating model design aligning people, processes, and technology around SAP
Vendor selection advisory for SAP implementation and managed service partners
Independent quality assurance and health checks for in-flight SAP programmes
Post-implementation reviews identifying unrealised value and optimisation opportunities
Change management and organisational readiness support for SAP transformations
Centre of Excellence design and establishment for long-term SAP self-sufficiency
Executive advisory and CIO support for complex SAP strategic decisions', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '225', NULL),
('47', '2', 'SAP Cyber Security', 'cyber-security', 'Protect Your Most Critical Business Systems and Data', 'fa-shield-halved', 'SAP systems sit at the heart of your organisation — hosting your most sensitive financial data, personal information, intellectual property, and operational processes. A security breach, ransomware attack, or insider threat targeting your SAP landscape can result in catastrophic financial losses, regulatory penalties, operational disruption, and irreparable reputational damage.', 'SAP Cyber Security is a specialised discipline that goes far beyond standard IT security — requiring deep knowledge of SAP architecture, authorisation concepts, transport management, and vulnerability patterns unique to SAP environments. Our dedicated SAP security practice helps organisations identify vulnerabilities, remediate risks, and build a robust, layered security framework that protects your SAP landscape against evolving internal and external threats.

Our certified SAP security consultants combine technical depth with regulatory expertise — delivering a comprehensive security posture that keeps your SAP systems resilient, compliant, and audit-ready at all times.', 'Comprehensive SAP security assessment identifying vulnerabilities across system, network, and application layers
Role and authorisation design, remediation, and Segregation of Duties (SoD) conflict resolution
SAP security note monitoring and critical patch implementation management
Penetration testing and ethical hacking specifically targeting SAP system vulnerabilities
SAP Identity and Access Management (IAM) design and implementation
User access reviews and periodic recertification campaigns ensuring least privilege principles
SAP Security Information and Event Management (SIEM) integration for real-time threat detection
Transport management security controls preventing unauthorised system changes
RFC and interface security hardening across SAP system landscapes
Protection against SAP-specific threats including ABAP code injection and privilege escalation
Compliance support for SOX, GDPR, ISO 27001, and industry-specific security regulations
Security awareness training and SAP-specific cyber hygiene programmes for IT and business users
Ongoing managed security monitoring and incident response for SAP environments', 'linear-gradient(135deg, #0B356D 0%, #1e1b4b 100%)', '230', NULL),
('48', '3', 'Automotive', 'ind-automotive', 'Accelerating smart assembly lines and parts logistic channels.', 'fa-car', 'The automotive industry requires absolute coordination across just-in-time delivery channels, raw materials tracing, and automated assembly equipment. Legacy paper sheets or disjointed software networks create severe lag and bottleneck risks.', 'LGS supplies dynamic enterprise architectures for automotive OEMs. We connect shop-floor PLCs, orchestrate material demands, and integrate core SAP Automotive templates to optimize material replenishments, leading to minimum warehouse overheads.', 'Just-in-Time (JIT) Supply Logistics
Assembly Speed PLC Connectors
Supply Chain Serial Tracking Rows', 'linear-gradient(135deg, #0B356D 0%, #0369a1 100%)', '10', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243237/localglobals/uploads/yvnqvfyqdn1xbi6kbi0d.jpg'),
('49', '3', 'Life Sciences & Pharma Tech', 'ind-lifesciences', 'Ensuring absolute chemical compliance, dynamic batch controls, and tracking records.', 'fa-dna', 'Local Global Services (LGS) delivers high-impact Life Sciences & Pharma Tech capability to help enterprises design, build, and support their core operational layers. Backed by twenty years of global consulting experience, we ensure seamless system integrations, automated data synchronizations, and robust compliance controls.', 'Our dedicated team of 500+ certified specialists is focused on driving tangible business outcomes, lower TCO, and unmatched agility. By aligning Life Sciences & Pharma Tech with your core business objectives, we deliver optimized operational performance, zero downtime conversions, and maximum ROI.', 'Tailored Industry Blueprints
Global Delivery Excellence
Continuous Support & Training
24/7 Monitoring & Optimization', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '20', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243240/localglobals/uploads/aop8dhkg4wneoav0ypn8.jpg'),
('50', '3', 'Engineer. Procure. Construct. Digitally.', 'ind-epc', 'Real-time project cost accounting, materials management, and logistics schedules for massive construct sectors.', 'fa-compass-drafting', 'Local Global Services (LGS) delivers high-impact Engineer. Procure. Construct. Digitally. capability to help enterprises design, build, and support their core operational layers. Backed by twenty years of global consulting experience, we ensure seamless system integrations, automated data synchronizations, and robust compliance controls.', 'Our dedicated team of 500+ certified specialists is focused on driving tangible business outcomes, lower TCO, and unmatched agility. By aligning Engineer. Procure. Construct. Digitally. with your core business objectives, we deliver optimized operational performance, zero downtime conversions, and maximum ROI.', 'Tailored Industry Blueprints
Global Delivery Excellence
Continuous Support & Training
24/7 Monitoring & Optimization', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '30', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243242/localglobals/uploads/i4hlnnk1re9lrtqdpdpl.jpg'),
('51', '3', 'Energy & Utility Services', 'ind-energy', 'Optimizing utility distribution networks, grid balances, and asset lifetime management.', 'fa-plug-circle-bolt', 'Local Global Services (LGS) delivers high-impact Energy & Utility Services capability to help enterprises design, build, and support their core operational layers. Backed by twenty years of global consulting experience, we ensure seamless system integrations, automated data synchronizations, and robust compliance controls.', 'Our dedicated team of 500+ certified specialists is focused on driving tangible business outcomes, lower TCO, and unmatched agility. By aligning Energy & Utility Services with your core business objectives, we deliver optimized operational performance, zero downtime conversions, and maximum ROI.', 'Tailored Industry Blueprints
Global Delivery Excellence
Continuous Support & Training
24/7 Monitoring & Optimization', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '40', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243244/localglobals/uploads/bbwyzlthebvy79go3g96.jpg'),
('52', '3', 'SAP for Manufacturing Excellence', 'ind-manufacturing', 'Transforming assembly centers into smart connected factories utilizing OEE telemetry.', 'fa-gears', 'Local Global Services (LGS) delivers high-impact SAP for Manufacturing Excellence capability to help enterprises design, build, and support their core operational layers. Backed by twenty years of global consulting experience, we ensure seamless system integrations, automated data synchronizations, and robust compliance controls.', 'Our dedicated team of 500+ certified specialists is focused on driving tangible business outcomes, lower TCO, and unmatched agility. By aligning SAP for Manufacturing Excellence with your core business objectives, we deliver optimized operational performance, zero downtime conversions, and maximum ROI.', 'Tailored Industry Blueprints
Global Delivery Excellence
Continuous Support & Training
24/7 Monitoring & Optimization', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '50', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243246/localglobals/uploads/ovrcactbiskbze1ikwhn.jpg'),
('53', '3', 'Real Estate', 'ind-realestate', 'Maximizing lease yields, property contract management, and construction tracking.', 'fa-building', 'Local Global Services (LGS) delivers high-impact Real Estate capability to help enterprises design, build, and support their core operational layers. Backed by twenty years of global consulting experience, we ensure seamless system integrations, automated data synchronizations, and robust compliance controls.', 'Our dedicated team of 500+ certified specialists is focused on driving tangible business outcomes, lower TCO, and unmatched agility. By aligning Real Estate with your core business objectives, we deliver optimized operational performance, zero downtime conversions, and maximum ROI.', 'Tailored Industry Blueprints
Global Delivery Excellence
Continuous Support & Training
24/7 Monitoring & Optimization', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '60', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243248/localglobals/uploads/b78upoi0fjtqdhbd8t2n.jpg'),
('54', '3', 'Retail Transformation Services', 'ind-retail', 'Unified omni-channel sales dashboards, POS data integrations, and automated supplier syncs.', 'fa-cart-shopping', 'Local Global Services (LGS) delivers high-impact Retail Transformation Services capability to help enterprises design, build, and support their core operational layers. Backed by twenty years of global consulting experience, we ensure seamless system integrations, automated data synchronizations, and robust compliance controls.', 'Our dedicated team of 500+ certified specialists is focused on driving tangible business outcomes, lower TCO, and unmatched agility. By aligning Retail Transformation Services with your core business objectives, we deliver optimized operational performance, zero downtime conversions, and maximum ROI.', 'Tailored Industry Blueprints
Global Delivery Excellence
Continuous Support & Training
24/7 Monitoring & Optimization', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '70', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243250/localglobals/uploads/erduwevsdevwrzrlb8eh.jpg'),
('55', '4', 'SAP e-Invoice Solution', 'sol-einvoice', 'Automate Invoicing and Ensure Regulatory Compliance', 'fa-file-invoice-dollar', 'SAP e-Invoice Solution digitalises and automates the entire invoicing process — from invoice generation and transmission to validation, submission to tax authorities, and archiving — ensuring your organisation meets increasingly stringent global e-invoicing mandates while driving significant efficiency gains across your finance function.', 'As governments worldwide accelerate the adoption of mandatory e-invoicing regulations, organisations must ensure their SAP systems are configured to generate, transmit, and receive compliant electronic invoices in the correct format for each country. Our SAP e-invoicing specialists help you navigate this complex regulatory landscape and implement a robust, scalable solution that keeps your business compliant today and future-ready for tomorrow.

Our consultants combine deep SAP Finance expertise with up-to-date knowledge of global e-invoicing standards — delivering a solution that integrates seamlessly with your existing SAP landscape and business processes.', 'End-to-end e-invoice generation, transmission, and receipt automation
Compliance with global e-invoicing mandates including GST, VAT, and tax authority requirements
Support for international e-invoicing standards including UBL, PEPPOL, and country-specific formats
Real-time invoice validation and error handling before submission to tax authorities
Seamless integration with SAP S/4HANA FI, SD, and MM modules
Automated clearance and post-audit e-invoicing model support
Secure, compliant long-term invoice archiving meeting legal retention requirements
Real-time visibility of invoice status across the entire procure-to-pay cycle
Supplier and customer portal for e-invoice exchange and query management
Multi-country and multi-currency support for global operations
Proactive monitoring of regulatory changes ensuring continuous compliance', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '10', NULL),
('56', '4', 'SAP e-Waybill Solution', 'sol-ewaybill', 'Seamless Compliance for Goods Movement', 'fa-truck-arrow-right', 'SAP e-Waybill Solution automates the generation, management, and submission of electronic waybills directly within your SAP environment — ensuring full compliance with government mandated e-waybill regulations while eliminating the manual effort, delays, and errors associated with paper-based goods movement documentation.', 'In markets where e-waybill compliance is mandatory, such as India''s GST e-Way Bill system, any lapse in documentation can result in penalties, shipment delays, and supply chain disruptions. Our SAP e-Waybill Solution integrates seamlessly with your SAP S/4HANA and logistics processes — automating e-waybill generation at the point of goods movement and ensuring every consignment is covered, traceable, and compliant.

Our consultants bring deep knowledge of e-waybill regulatory requirements and SAP logistics processes — delivering a robust, end-to-end solution tailored to your supply chain operations.', 'Automated e-waybill generation triggered directly from SAP goods movement transactions
Seamless integration with SAP S/4HANA, MM, SD, and Logistics Execution modules
Real-time API connectivity with government e-waybill portals and tax authorities
Support for all e-waybill transaction types including inward, outward, and job work
Automated e-waybill extension, cancellation, and rejection handling within SAP
Vehicle and transporter details capture with part-B update capabilities
Bulk e-waybill generation for high-volume goods movement operations
Real-time e-waybill status tracking and expiry alerts to prevent compliance breaches
Consolidated e-waybill reports and compliance dashboards for finance and logistics teams
Multi-plant and multi-location support for complex supply chain operations
Proactive regulatory update monitoring ensuring continuous compliance with changing rules', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '20', NULL),
('57', '4', 'GST Reconciliation', 'sol-gst', 'Ensure Accuracy, Compliance, and Maximum Input Tax Credit Recovery', 'fa-receipt', 'GST Reconciliation is a critical compliance process that matches your organisation''s purchase and sales data recorded in SAP against the returns filed by your suppliers on the government GST portal — ensuring accuracy, identifying discrepancies, and maximising your eligible Input Tax Credit (ITC) claims.', 'In an environment of increasing GST scrutiny and automated tax authority matching, organisations that rely on manual reconciliation processes face significant risks — including ITC mismatches, compliance notices, financial penalties, and cash flow impact. Our SAP GST Reconciliation solution automates this process end-to-end, giving your finance team the visibility, control, and confidence to meet every filing deadline accurately and efficiently.

Our GST and SAP Finance specialists combine deep tax knowledge with technical expertise — delivering a reconciliation framework that integrates seamlessly with your SAP landscape and GST compliance workflows.', 'Automated matching of SAP purchase data against supplier GSTR-2A and GSTR-2B returns
Real-time identification of mismatches, missing invoices, and ITC discrepancies
Maximisation of eligible Input Tax Credit recovery reducing overall tax liability
Automated vendor communication workflows for mismatch resolution and follow-up
Seamless integration with SAP S/4HANA FI, MM, and Tax modules
Support for all GST return types including GSTR-1, GSTR-3B, GSTR-2A, and GSTR-2B
Reconciliation dashboards providing real-time compliance status and exception reporting
Audit-ready reconciliation reports supporting GST assessments and departmental queries
Multi-GSTIN support for organisations operating across multiple states and entities
Proactive alerts for unreconciled invoices approaching filing deadlines
Continuous monitoring of GST regulatory changes ensuring ongoing compliance', 'linear-gradient(135deg, #0B356D 0%, #0369a1 100%)', '30', NULL),
('58', '4', 'SAP Gate Entry Exit Solution', 'sol-gateentry', 'Digitise and Control Every Movement Across Your Premises', 'fa-door-open', 'SAP Gate Entry Exit Solution automates and digitises the management of all inbound and outbound movements across your facility — covering vehicles, materials, visitors, and contractors — and integrates these movements seamlessly with your SAP business processes in real time.', 'Replacing manual gate registers, paper-based documentation, and disconnected spreadsheets, our solution creates a fully digital, auditable, and controlled gate management process that improves security, accelerates turnaround times, and ensures every goods movement is accurately reflected in SAP — from purchase order receipt and stock transfers to customer deliveries and vendor returns.

Built on SAP BTP and integrated with SAP S/4HANA, our Gate Entry Exit Solution is tailored to your facility layout, operational workflows, and compliance requirements — delivering a seamless experience for gate operators, security personnel, and logistics teams alike.', 'Digital gate entry and exit management replacing manual paper-based registers
Real-time integration with SAP S/4HANA MM, SD, WM, and Logistics modules
Automated inbound goods receipt and outbound delivery processing at the gate
Vehicle registration, driver identification, and transporter management
Material verification against purchase orders, delivery notes, and transfer orders
Visitor and contractor management with digital pass issuance and tracking
RFID, barcode, and QR code scanning for fast and accurate vehicle identification
Weighbridge integration for automated gross and tare weight capture at entry and exit
Real-time gate movement dashboards and vehicle turnaround time analytics
Automated alerts for unauthorised movements, overdue vehicles, and discrepancies
Full audit trail of all gate transactions supporting security and compliance requirements
Multi-gate and multi-plant support for large and complex facility operations', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '40', NULL),
('59', '4', 'SAP Barcode Integration', 'sol-barcode', 'Accelerate Operations and Eliminate Data Entry Errors', 'fa-barcode', 'SAP Barcode Integration connects barcode scanning technology directly with your SAP environment — automating the capture and processing of material, asset, and transaction data at the point of activity. It eliminates manual data entry, reduces processing errors, and accelerates warehouse, production, and logistics operations by enabling real-time SAP transactions through simple, intuitive scanning workflows.', 'Whether managing goods receipts, stock transfers, picking and packing, production confirmations, or asset tracking, barcode integration ensures every movement is captured instantly and accurately in SAP — giving your operations teams real-time inventory visibility and your management teams the data confidence to make better decisions faster.

Our development team designs and implements a tailored barcode integration solution that aligns with your operational workflows, hardware infrastructure, and SAP system landscape — delivering measurable improvements in speed, accuracy, and productivity from day one.', 'Real-time barcode scanning integrated directly with SAP S/4HANA transactions
Support for 1D barcodes, 2D QR codes, and GS1 standard label formats
Automated goods receipt, goods issue, and stock transfer processing via scan
Warehouse management operations including picking, packing, and putaway
Production order confirmations and backflushing triggered through barcode scanning
Fixed asset tracking and verification using barcode and QR code labels
Mobile scanning support via handheld devices, tablets, and wearable scanners
Offline scanning capability ensuring operations continue during network interruptions
Integration with SAP S/4HANA MM, WM, EWM, PP, and Asset Management modules
Label design and printing automation for materials, pallets, and finished goods
Real-time inventory accuracy dashboards and exception reporting
Significant reduction in manual data entry errors and stock discrepancies', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '50', NULL),
('60', '4', 'SAP WhatsApp Integration', 'sol-whatsapp', 'Real-Time Business Communication Powered by SAP', 'fa-whatsapp', 'SAP WhatsApp Integration connects the world''s most widely used messaging platform directly with your SAP environment — enabling real-time, automated, and personalised business communications to be triggered seamlessly from SAP transactions and workflows. It transforms how your organisation engages with customers, suppliers, employees, and partners by delivering critical business notifications, approvals, and updates instantly through a channel they already use every day.', 'From purchase order confirmations and delivery notifications to payment reminders, approval requests, and customer service interactions, SAP WhatsApp Integration eliminates communication delays, reduces dependency on email, and drives faster responses and actions across your entire business ecosystem.

Built on WhatsApp Business API and integrated with SAP BTP and SAP S/4HANA, our solution is designed around your specific business workflows — delivering a secure, scalable, and personalised messaging experience that improves engagement, accelerates processes, and enhances stakeholder satisfaction.', 'Real-time automated WhatsApp notifications triggered directly from SAP transactions
Purchase order, delivery, invoice, and payment status updates via WhatsApp
Workflow approval requests with accept and reject actions directly within WhatsApp
Customer order confirmations, shipment tracking, and delivery alerts
Supplier communication automation for PO acknowledgements and invoice queries
Employee notifications for leave approvals, payroll confirmations, and HR updates
Two-way messaging enabling recipients to respond and trigger SAP actions
Chatbot integration for self-service queries on order status, inventory, and more
Built on WhatsApp Business API ensuring secure and compliant message delivery
Seamless integration with SAP S/4HANA, MM, SD, FI, and HR modules
Multi-language message support for global and multilingual operations
Real-time message delivery tracking and communication analytics dashboard', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '60', NULL),
('61', '4', 'Payment Gateway Integration', 'sol-payment', 'Seamless, Secure, and Automated Payment Processing Within SAP', 'fa-credit-card', 'Payment Gateway Integration connects leading payment platforms directly with your SAP environment — automating the end-to-end payment process from transaction initiation and authorisation to reconciliation and posting in SAP. It eliminates manual payment handling, reduces settlement delays, and provides real-time visibility of payment status across all channels and entities within your organisation.', 'Whether processing customer payments, vendor disbursements, or employee reimbursements, our Payment Gateway Integration solution ensures every transaction is securely authorised, accurately recorded in SAP, and fully reconciled — reducing financial risk, improving cash flow visibility, and delivering a seamless payment experience for your customers and stakeholders.

Built on SAP BTP and integrated with SAP S/4HANA Finance, our solution supports multiple payment gateways, currencies, and payment methods — providing a flexible, scalable architecture that grows with your business and adapts to evolving payment regulations and technology standards.', 'Seamless integration with leading payment gateways including Razorpay, PayU, CCAvenue, Stripe, and PayPal
Automated payment initiation, authorisation, and confirmation directly within SAP workflows
Real-time payment status updates and automated SAP document posting upon settlement
Support for multiple payment methods including credit cards, debit cards, UPI, net banking, and wallets
Automated bank reconciliation matching payment gateway settlements with SAP Finance entries
Secure tokenisation and encryption ensuring PCI DSS compliance and data protection
Multi-currency and multi-entity payment processing for global operations
Customer payment portal with real-time invoice viewing and online payment capability
Automated payment failure handling, retry logic, and exception management workflows
Refund processing and credit note generation directly triggered from SAP transactions
Real-time payment analytics and cash flow dashboards for finance leadership
Comprehensive audit trail of all payment transactions supporting financial compliance and reporting', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '70', NULL),
('62', '4', 'P2P Procurement Automation', 'sol-p2p', 'Transform Procure-to-Pay into a Strategic Business Advantage', 'fa-arrows-spin', 'Procure-to-Pay (P2P) Automation digitalises and streamlines every step of the procurement cycle — from purchase requisition and supplier selection to purchase order creation, goods receipt, invoice processing, and vendor payment — within a single, integrated SAP environment. It eliminates manual touchpoints, reduces processing costs, and gives procurement and finance teams complete visibility and control over every spend decision.', 'Organisations with fragmented, manual P2P processes face significant challenges — maverick spending, delayed approvals, invoice mismatches, and poor supplier relationships. Our P2P Automation solution addresses these challenges head-on by embedding intelligent automation, workflow controls, and real-time analytics directly into your SAP procurement and finance processes — transforming P2P from an administrative burden into a strategic driver of cost savings and operational efficiency.

Our SAP procurement specialists bring deep functional expertise across SAP MM, Ariba, and S/4HANA Finance — delivering an end-to-end P2P automation framework tailored to your procurement policies, approval hierarchies, and supplier management requirements.', 'End-to-end automation covering requisition, sourcing, ordering, receipt, and payment
Automated purchase requisition creation and intelligent approval routing workflows
RFQ and sourcing automation for competitive supplier selection and pricing
Automated purchase order generation, transmission, and supplier acknowledgement
Three-way matching of purchase orders, goods receipts, and vendor invoices
Intelligent invoice capture using OCR and AI-powered data extraction
Exception management workflows for invoice mismatches and discrepancy resolution
Real-time spend visibility and procurement analytics across all categories and suppliers
Supplier self-service portal for purchase order confirmation and invoice submission
Seamless integration with SAP S/4HANA, SAP Ariba, and OpenText VIM
Compliance controls ensuring adherence to procurement policies and approval limits
Significant reduction in invoice processing costs and purchase order cycle times', 'linear-gradient(135deg, #0B356D 0%, #031D44 100%)', '80', NULL);

-- --------------------------------------------------------
-- Table structure for `settings`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  `key` VARCHAR(100) UNIQUE NOT NULL,
  value TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for `settings`
INSERT INTO `settings` (`id`, `key`, `value`) VALUES
('1', 'contact_phone', '+91-9718117270'),
('2', 'contact_email', 'sales@localglobal.com'),
('5', 'popup_status', 'show'),
('6', 'popup_type', 'image'),
('7', 'popup_title', 'Welcome to Local Global Services!'),
('8', 'popup_text', 'Discover our next-generation SAP solutions and cognitive AI integrations.'),
('9', 'popup_image', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243265/localglobals/uploads/pebkrbhwulncxfyioqde.jpg'),
('24', 'hero_bg_image', 'resources/hero-bg.png'),
('25', 'hero_bg_opacity', '0.45'),
('26', 'about_title', 'Who We Are'),
('27', 'about_subtitle', 'Your Trusted Technology Integrator & Digital Transformation Catalyst.'),
('28', 'about_desc_1', 'Local Global Services (LGS) bridges the gap between traditional operations and modern digital technology stacks. Backed by twenty years of global consulting experience, we help enterprises design, build, and support their core operational systems.'),
('29', 'about_desc_2', 'We focus on building customer-centric digital solutions using high-performing tools like SAP ERP, cellular IoT tracking, and cognitive AI tools. Our dedicated team of 500+ certified specialists is focused on driving tangible business outcomes, lower TCO, and unmatched agility.'),
('30', 'about_image', 'resources/wo-we-are.png');

-- --------------------------------------------------------
-- Table structure for `blogs`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `blogs`;
CREATE TABLE IF NOT EXISTS blogs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(100) UNIQUE NOT NULL,
  summary TEXT NOT NULL,
  content TEXT NOT NULL,
  seo_title VARCHAR(255) NOT NULL,
  meta_description TEXT NOT NULL,
  image_url VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  author VARCHAR(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for `blogs`
INSERT INTO `blogs` (`id`, `title`, `slug`, `summary`, `content`, `seo_title`, `meta_description`, `image_url`, `created_at`, `author`) VALUES
('1', 'Accelerating Enterprise Value with SAP S/4HANA Cloud', 'accelerating-enterprise-value-sap-s4hana', 'Explore how migrating to SAP S/4HANA Cloud empowers organizations with real-time analytics, automated workflows, and lower total cost of ownership.', '<p>Modern enterprises operate in a highly dynamic and hyper-competitive global market. Legacy ERP systems, once the bedrock of business operations, often struggle to keep pace with today''s demands for real-time visibility, agility, and speed. This is where <strong>SAP S/4HANA Cloud</strong> steps in, offering a next-generation cloud-ERP platform that transforms operations.</p><h4>Why Migration is a Strategic Imperative</h4><p>Migrating to SAP S/4HANA Cloud is not merely an IT upgrade; it is a profound business transformation. By shifting core databases to the in-memory SAP HANA database, organizations can process massive volumes of operational data instantly. This enables real-time reporting, immediate financial close cycles, and automated predictive analytics.</p><h4>Key Benefits of S/4HANA Cloud</h4><ul><li><strong>Enhanced Agility:</strong> Instantly adapt to market changes with intelligent forecasting and integrated supplier networks.</li><li><strong>Reduced TCO:</strong> Shift from heavy capital expenditures to a predictable operational expense model with cloud hosting.</li><li><strong>Standardized Processes:</strong> Leverage built-in best practices for finance, HR, procurement, and manufacturing to eliminate custom code complexity.</li></ul><p>Partnering with a certified global integrator like Local Global Services (LGS) ensures a smooth transition using greenfield or brownfield migration strategies, minimizing downtime and accelerating time-to-value.</p>', 'SAP S/4HANA Cloud Migration Benefits & Strategy | LGS', 'Discover the strategic advantages of migrating to SAP S/4HANA Cloud. Learn how real-time databases and standardized cloud-ERP systems drive enterprise agility.', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243253/localglobals/uploads/j1dd0pw0viildtsy4okc.png', '2026-06-06 08:38:58', NULL),
('2', 'Industrial IoT: Bridging the Gap Between Factory Floors and Cloud Ledgers', 'industrial-iot-factory-floor-to-cloud', 'Discover how connecting machinery PLCs and IoT edge sensors with your core ERP drives real-time OEE visibility and proactive maintenance scheduling.', '<p>For decades, the factory floor and the corporate boardroom existed in separate silos. High-frequency machinery logs, temperature values, and production counts were tracked locally on standalone screens, while finance and planning sat in centralized databases. Today, <strong>Industrial IoT (IIoT)</strong> connects these two worlds, creating a unified flow of operational intelligence.</p><h4>The Power of Edge Analytics</h4><p>By deploying ruggedized sensors, cellular gateway transceivers, and PLC data collectors, manufacturers can capture raw telemetry from machinery in real-time. Edge gateways process this data immediately to calculate Overall Equipment Effectiveness (OEE), flag thermal drift, or detect motor vibration anomalies.</p><h4>Seamless Integration with SAP ERP</h4><p>When IoT data flows directly into the core ERP, the business benefits are immediate:</p><ul><li><strong>Predictive Maintenance:</strong> Automatically trigger maintenance work orders in SAP PM when a machine exceeds standard operating thresholds, preventing costly breakdown downtime.</li><li><strong>Automated Production Auditing:</strong> Product counts are updated in the inventory ledger instantly as they roll off the assembly line, avoiding manual count errors.</li><li><strong>Resilient Supply Chain:</strong> Link machine output directly to raw material procurement queues.</li></ul><p>By connecting hardware with enterprise data streams, businesses gain complete transparency and achieve true manufacturing excellence.</p>', 'Industrial IoT and ERP Integration Guide | LGS', 'Learn how Industrial IoT (IIoT) integrates factory floor telemetry and machine learning with ERP databases to automate maintenance and track real-time OEE.', 'https://res.cloudinary.com/dbxfs9npx/image/upload/v1717672200/iot_manufacturing_blog.jpg', '2026-06-06 08:38:58', NULL),
('3', 'Generative AI and Cognitive Workflows in Modern Finance', 'generative-ai-cognitive-workflows-finance', 'How cognitive document models and machine learning classifiers automate accounts payable processing, eliminating manual invoice entry errors.', '<p>Accounts Payable (AP) departments are traditionally burdened with processing hundreds of invoices daily. Manual data entry, finding billing mismatches, and route approvals slow down cash cycles and increase compliance risks. <strong>Cognitive AI and Generative Models</strong> are rewriting this script, turning document entry into a touchless process.</p><h4>The Anatomy of Cognitive AP Automation</h4><p>Modern AP automation solutions leverage advanced machine learning models trained on millions of document types. These models do not rely on fragile coordinate-based templates. Instead, they read invoices contextually, extracting billing headers, vendor credentials, line items, and tax breakdowns with near-perfect accuracy.</p><h4>Achieving Three-Way Automated Matching</h4><p>Once the data is extracted, cognitive workflows integrate with core databases to execute three-way matches automatically:</p><ol><li>Verify the invoice against the original <strong>Purchase Order (PO)</strong> raised in the ERP.</li><li>Compare the items and quantities against the <strong>Goods Receipt (GR)</strong> recorded at the warehouse gate.</li><li>Automatically approve the invoice for payment if all values align, flagging exceptions for human review.</li></ol><p>LGS deploys intelligent invoice automation suites that connect seamlessly to SAP ERP databases, helping businesses reduce processing time by up to 80% while maintaining absolute audit transparency.</p>', 'AI Invoice & Accounts Payable Automation | LGS', 'Explore how cognitive machine learning models and AI-driven invoice automation eliminate manual data entry errors and accelerate accounts payable workflows.', 'https://res.cloudinary.com/dbxfs9npx/image/upload/v1717672200/ai_finance_blog.jpg', '2026-06-06 08:38:58', NULL),
('4', 'what is SAP', 'what-is-sap', 'this is about sap hana', '<h2 class="section-title mb-4" data-lang-key="about_subtitle" style="margin-bottom: 12px; line-height: 1.6; color: rgb(71, 85, 105); font-size: 1.05rem; font-family: Inter, sans-serif; position: relative; max-width: 800px;">Your Trusted Technology Integrator &amp; Digital Transformation Catalyst.</h2><p class="mb-3 lead text-muted" data-lang-key="about_desc_1" style="color: rgb(71, 85, 105); font-family: Inter, sans-serif; font-size: 16px;">Local Global Services (LGS) bridges the gap between traditional operations and modern digital technology stacks. Backed by twenty years of global consulting experience, we help enterprises design, build, and support their core operational systems.</p><p class="mb-4 text-muted" data-lang-key="about_desc_2" style="font-family: Inter, sans-serif; color: rgba(33, 37, 41, 0.75) !important;">We focus on building customer-centric digital solutions using high-performing tools like SAP ERP, cellular IoT tracking, and cognitive AI tools. Our dedicated team of 500+ certified specialists is focused on driving tangible business outcomes, lower TCO, and unmatched agility.</p><ul class="about-features-list" style="color: rgb(15, 23, 42); font-family: Inter, sans-serif;"><li style="color: rgb(71, 85, 105);"><span class="fa-solid fa-circle-check text-danger" style="display: inline-block; color: rgb(220, 53, 69) !important;"></span>&nbsp;<span data-lang-key="about_feat_1">Global Delivery Excellence</span></li><li style="color: rgb(71, 85, 105);"><span class="fa-solid fa-circle-check text-danger" style="display: inline-block; color: rgb(220, 53, 69) !important;"></span>&nbsp;<span data-lang-key="about_feat_2">Tailored Industry Blueprints</span></li><li style="color: rgb(71, 85, 105);"><span class="fa-solid fa-circle-check text-danger" style="display: inline-block; color: rgb(220, 53, 69) !important;"></span>&nbsp;<span data-lang-key="about_feat_3">Continuous Support &amp; Training</span></li></ul>', 'what is hana get detail guide', 'descriptio', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243256/localglobals/uploads/lzmccno2lo8mfquj2kyy.png', '2026-06-08 11:16:22', NULL),
('5', 'test', 'test', 'helo', '<div class="section-header text-center max-width-600 mx-auto" style="color: rgb(15, 23, 42); font-family: Inter, sans-serif; background-color: rgb(248, 249, 250);"><span class="section-tagline" data-lang-key="ind_title" style="font-family: Outfit, sans-serif; color: rgb(11, 53, 109); font-weight: 800; font-size: 1.85rem; letter-spacing: -0.5px; margin-bottom: 4px; display: inline-flex; align-items: center; gap: 10px; position: relative;">Industries We Empower</span><h2 class="section-title mb-3" data-lang-key="ind_subtitle" style="margin-bottom: 12px; line-height: 1.6; color: rgb(71, 85, 105); font-size: 1.05rem; font-family: Inter, sans-serif; letter-spacing: 0px; position: relative; max-width: 800px;">Providing tailored, business-aligned software frameworks across global economic sectors.</h2></div><div class="text-center mb-5" style="color: rgb(15, 23, 42); font-family: Inter, sans-serif; background-color: rgb(248, 249, 250);"><div class="nav nav-pills nav-pills-custom" id="industryPills" role="tablist" style="--bs-nav-link-padding-y: 0.5rem; --bs-nav-link-font-weight: ; --bs-nav-link-color: #0d6efd; --bs-nav-link-hover-color: #0a58ca; --bs-nav-link-disabled-color: rgba(33, 37, 41, 0.75); display: inline-flex; padding: 6px; --bs-nav-pills-border-radius: 0.375rem; --bs-nav-pills-link-active-color: #fff; --bs-nav-pills-link-active-bg: #0d6efd; border-radius: 50px; border: 1px solid rgba(0, 0, 0, 0.04); justify-content: center;"><button class="nav-link active" id="industry-tab-1" data-bs-toggle="pill" data-bs-target="#industry-pane-1" type="button" role="tab" aria-controls="industry-pane-1" aria-selected="true" style="border-radius: 50px; font-size: 0.9rem; font-weight: 600; transition: 0.3s cubic-bezier(0.16, 1, 0.3, 1); font-family: Inter, sans-serif !important; padding: 10px 24px !important; color: rgb(255, 255, 255) !important; background-color: rgb(11, 53, 109) !important;"><span class="fa-solid fa-industry me-2" style="display: inline-block;"></span>&nbsp;Manufacturing</button><button class="nav-link " id="industry-tab-2" data-bs-toggle="pill" data-bs-target="#industry-pane-2" type="button" role="tab" aria-controls="industry-pane-2" aria-selected="false" tabindex="-1" style="border-radius: 50px; font-size: 0.9rem; font-weight: 600; color: rgb(71, 85, 105); transition: 0.3s cubic-bezier(0.16, 1, 0.3, 1); font-family: Inter, sans-serif !important; padding: 10px 24px !important;"><span class="fa-solid fa-basket-shopping me-2" style="display: inline-block;"></span>&nbsp;Retail &amp; Consumer</button><button class="nav-link " id="industry-tab-3" data-bs-toggle="pill" data-bs-target="#industry-pane-3" type="button" role="tab" aria-controls="industry-pane-3" aria-selected="false" tabindex="-1" style="border-radius: 50px; font-size: 0.9rem; font-weight: 600; color: rgb(71, 85, 105); transition: 0.3s cubic-bezier(0.16, 1, 0.3, 1); font-family: Inter, sans-serif !important; padding: 10px 24px !important;"><span class="fa-solid fa-bolt me-2" style="display: inline-block;"></span>&nbsp;Energy &amp; Utilities</button><button class="nav-link " id="industry-tab-4" data-bs-toggle="pill" data-bs-target="#industry-pane-4" type="button" role="tab" aria-controls="industry-pane-4" aria-selected="false" tabindex="-1" style="border-radius: 50px; font-size: 0.9rem; font-weight: 600; color: rgb(71, 85, 105); transition: 0.3s cubic-bezier(0.16, 1, 0.3, 1); font-family: Inter, sans-serif !important; padding: 10px 24px !important;"><span class="fa-solid fa-truck-fast me-2" style="display: inline-block;"></span>&nbsp;Logistics &amp; Supply</button></div></div><div class="tab-content" id="industryPillsContent" style="color: rgb(15, 23, 42); font-family: Inter, sans-serif; background-color: rgb(248, 249, 250);"><div class="tab-pane fade show active" id="industry-pane-1" role="tabpanel" aria-labelledby="industry-tab-1"><div class="tab-pane-content" style="background: none 0% 0% / auto repeat scroll padding-box border-box rgb(255, 255, 255); border-radius: 24px; box-shadow: rgba(11, 53, 109, 0.04) 0px 10px 15px -3px, rgba(11, 53, 109, 0.04) 0px 4px 6px -4px; border: 1px solid rgba(0, 0, 0, 0.04); padding: 40px; margin-top: 30px;"><div class="row align-items-center g-4" style="--bs-gutter-y: 1.5rem; margin-top: -24px; margin-right: -12px; margin-left: -12px;"><div class="col-md-7" style="width: 722.4px; padding-right: 12px; padding-left: 12px; margin-top: 24px;"><h3 class="fw-bold mb-3" style="color: rgb(11, 53, 109); letter-spacing: -0.5px;">Manufacturing</h3><p class="text-muted mb-0 lead" style="color: rgb(71, 85, 105); font-size: 1.05rem;">Optimize shop floor automation, implement advanced OEE dashboards, and track materials using localized SAP PLM modules.</p></div><div class="col-md-5 text-center" style="width: 516px; padding-right: 12px; padding-left: 12px; margin-top: 24px;"><div style="font-size: 8rem; color: rgba(11, 53, 109, 0.08);"><span class="fa-solid fa-industry" style="display: inline-block;"></span></div></div></div></div></div></div>', 'teh', 'bgvhdgm', NULL, '2026-06-08 13:06:28', 'gd');

-- --------------------------------------------------------
-- Table structure for `testimonials`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `testimonials`;
CREATE TABLE IF NOT EXISTS testimonials (
  id INT AUTO_INCREMENT PRIMARY KEY,
  client_name VARCHAR(255) NOT NULL,
  service_name VARCHAR(255) NOT NULL,
  testimonial_text TEXT NOT NULL,
  image_url VARCHAR(255) DEFAULT NULL,
  sort_order INTEGER DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for `testimonials`
INSERT INTO `testimonials` (`id`, `client_name`, `service_name`, `testimonial_text`, `image_url`, `sort_order`, `created_at`) VALUES
('1', 'Sarah Jenkins', 'SAP S/4HANA Cloud', 'LGS executed our global S/4HANA brownfield migration with zero operations downtime. Truly an elite Gold partner.', NULL, '10', '2026-06-08 12:44:06'),
('2', 'Ahmed Al-Sayed', 'RISE with SAP & IoT', 'Our fleet operations OEE increased by 22% within 90 days of deploying LGS cellular tracking telemetry.', NULL, '20', '2026-06-08 12:44:06'),
('3', 'Michael Chen', 'AI AP Invoice Automation', 'The cognitive AP invoice automation has reduced our manual invoice processing times by 80%. Outstanding ROI!', NULL, '30', '2026-06-08 12:44:06'),
('4', 'ohn', 'ct', 'hello this is ery e', NULL, '10', '2026-06-08 13:07:03'),
('6', 'eduward', 'SAP ERP', 'sap erp was the sap erp was the sap erp was the sap erp was the sap erp was the', NULL, '10', '2026-06-08 13:18:14'),
('7', 'eduward', 'SAP ERP', 'sap erp was the sap erp was the sap erp was the sap erp was the sap erp was the', NULL, '10', '2026-06-08 13:26:32'),
('8', 'eduward', 'SAP ERP', 'sap erp was the sap erp was the sap erp was the sap erp was the sap erp was the', NULL, '10', '2026-06-08 13:29:58');

-- --------------------------------------------------------
-- Table structure for `industries`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `industries`;
CREATE TABLE IF NOT EXISTS industries (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  icon VARCHAR(100) NOT NULL,
  sort_order INTEGER DEFAULT 0,
  image_url VARCHAR(255) DEFAULT NULL,
  features TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for `industries`
INSERT INTO `industries` (`id`, `name`, `title`, `description`, `icon`, `sort_order`, `image_url`, `features`) VALUES
('1', 'Manufacturing', 'Manufacturing', 'Optimize shop floor automation, implement advanced OEE dashboards, and track materials using localized SAP PLM modules.', 'fa-industry', '10', 'https://res.cloudinary.com/dsudiynxi/image/upload/v1781243259/localglobals/uploads/qfjukymn5fyrzvaujemc.png', 'Optimize shop floor automation
Implement advanced OEE dashboards
Track materials using localized SAP PLM'),
('2', 'Retail & Consumer', 'Retail & Consumer', 'Integrate unified omni-channel customer experiences, coordinate supply networks, and automate restocking channels.', 'fa-basket-shopping', '20', NULL, 'Integrate unified omni-channel CX
Coordinate global supply networks
Automate restocking channels'),
('3', 'Energy & Utilities', 'Energy & Utilities', 'Ensure smart grid integrations, optimize resource exploration processes, and manage global asset life cycles.', 'fa-bolt', '30', NULL, 'Ensure smart grid integrations
Optimize resource exploration
Manage global asset life cycles'),
('4', 'Logistics & Supply', 'Logistics & Supply', 'Maximize fleet utilization, integrate real-time cellular tracking telemetry, and lower overall warehousing overheads.', 'fa-truck-fast', '40', NULL, 'Maximize fleet utilization
Real-time cellular tracking telemetry
Lower warehousing overheads');

SET FOREIGN_KEY_CHECKS = 1;
