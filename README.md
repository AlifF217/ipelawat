# iPelawat - Visitor Registration System
## 🛠️ Technologies Used
PHP - Primary programming language

CodeIgniter 4 - PHP framework for web application development

MySQL - Relational database management system

Apache - Web server for hosting the application

HTML5 & CSS3 - Frontend markup and styling

JavaScript - Client-side interactivity and dynamic features

Bootstrap - Responsive UI framework

QR Code Library - QR code generation for visitor registration

WhatsApp API Integration - Communication and notification system

## ✨ Features
## 🔐 Authentication System
Secure login for administrators and SuperAdmin

Multi-level access control (Pelawat, Pentadbir, SuperAdmin)

Password hashing for security

Session management

## 📋 Core Registration Modules
1. Visitor Registration
Self-registration for external visitors (Pelawat Luar)

Registration for internal visitors (Pelawat Dalaman)

Registration hours: 8:00 AM - 6:00 PM

QR code generation for quick access

WhatsApp integration for registration links

2. Manual Registration
Administrators can register visitors manually

Update visitor information as needed

View and manage registration details

3. Dashboard & Analytics
Real-time visitor statistics

Daily, weekly, monthly, and yearly visitor counts

Comparison between external and internal visitors

Top administrator performance metrics

4. Calendar View
Visual calendar display of visitor registrations

Search and filter functionality

Quick access to registration records

5. Administrator Management
View list of all administrators

Add, edit, and manage administrator accounts

Role-based permissions (Pentadbir vs SuperAdmin)

Only SuperAdmin can delete administrator accounts

6. Profile Management
View and update personal information

Password management

Profile picture upload

## 👤 User Roles
Role	Permissions
Pelawat	Register, update own data, view QR code, WhatsApp sharing
Pentadbir	Dashboard access, manage visitors, manual registration, calendar search
SuperAdmin	All Pentadbir functions + delete data, manage administrators
## 📊 Key Modules
Registration Module - Quick and efficient visitor registration

Update Module - Edit visitor information before 6:00 PM

QR Code Module - Generate and share registration links

WhatsApp Integration - Send registration links via WhatsApp

Statistics Dashboard - Comprehensive visitor analytics

Calendar Module - Search and manage records by date

Administrator Module - Manage admin accounts and divisions

Profile Module - Personal information management

## 📖 How to Use It
### Getting Started
Access the System - Open Google Chrome or Microsoft Edge browser

Login - Enter credentials for registered administrator accounts

Dashboard - View real-time visitor statistics upon login

### Registration Process
#### For Visitors (Pelawat)
Scan QR code or access registration link

Fill in required information:

Full name

Phone number

Officer visited

Purpose of visit

Time in (auto-recorded)

Submit registration

Receive confirmation with QR code

Update information if needed (before 6:00 PM)

#### For Administrators (Pentadbir)
Dashboard Access

View visitor statistics

Monitor daily registrations

Track performance metrics

Manual Registration

Register visitors who don't have mobile access

Update visitor information

Generate QR codes

Record Management

Search records via calendar

Update visitor information

View detailed logs

Administration

Manage administrator accounts (SuperAdmin only)

Add/Edit divisions

View visitor lists

Administrator Registration (First Time)
Navigate to registration page

Create new administrator account

Set user level (Pentadbir or SuperAdmin)

Log in with new credentials

## 🏗️ How It Is Built
Architecture
The system follows a Model-View-Controller (MVC) architecture using CodeIgniter 4:
<coming soon!>

## Key Components
Controllers
AuthController - Login, registration, logout

VisitorController - Registration, update, QR code

DashboardController - Statistics display

AdminController - Administrator management

ProfileController - User profile management

CalendarController - Calendar search functionality

Models
BookingModel - Visitor registration data

GuestModel - External visitor information

UserModel - Administrator/SuperAdmin data

DivisionModel - Department/division management

Database Structure
sql
-- Booking Table (Visitor Registration)
CREATE TABLE booking (
    booking_id INTEGER PRIMARY KEY,
    name VARCHAR(50),
    phone_no VARCHAR(50),
    officer VARCHAR(50),
    reason VARCHAR(50),
    time_in TIMESTAMP,
    time_out_exp TIMESTAMP,
    time_out_real TIMESTAMP,
    pelawat VARCHAR(50)
);

-- Divisions Table
CREATE TABLE divisions (
    id INTEGER PRIMARY KEY,
    name VARCHAR(100),
    parent_id INTEGER,
    created_at TIMESTAMP
);

-- Guest Table (External Visitors)
CREATE TABLE guest (
    id INTEGER PRIMARY KEY,
    name VARCHAR(100),
    tel VARCHAR(50),
    reason VARCHAR(50)
);

-- Users Table (Administrators/SuperAdmin)
CREATE TABLE users (
    id INTEGER PRIMARY KEY,
    name VARCHAR(100),
    password VARCHAR(255),
    email VARCHAR(255),
    division VARCHAR(50),
    phone VARCHAR(20),
    level ENUM('pentadbir', 'superadmin'),
    profilePicture VARCHAR(255),
    active VARCHAR(255)
);
Key Technologies Integration
QR Code Integration
Generates unique QR codes for each registration

Scannable codes link directly to registration forms

Automatic time recording upon scanning

WhatsApp Integration
One-click sharing of registration links

Quick communication with visitors

Contact details included in messages

Security Features
Password hashing

Session-based authentication

Role-based access control

Input validation and sanitization

## 📚 What I Learned
### Technical Skills
Skill	Description
CodeIgniter 4	MVC framework development, routing, and configuration
PHP Web Development	Server-side programming and business logic implementation
MySQL Database Design	ERD creation, table relationships, query optimization
QR Code Implementation	Generating and integrating QR codes for registration
API Integration	WhatsApp API for communication features
Responsive UI Design	Bootstrap implementation and mobile-friendly interfaces
Apache Server Configuration	Web server setup and deployment
Session Management	Secure user session handling
Development Practices
User-Centered Design - Features based on actual organizational needs

System Analysis - Requirements gathering and documentation

Iterative Development - Building and refining based on feedback

User Acceptance Testing - Practical system validation with end-users

Documentation - Comprehensive technical and user documentation

Project Management
Gantt Chart Planning - Project timeline and milestone tracking

Requirements Elicitation - Understanding user needs through stakeholder interviews

System Design - Creating architecture, ERD, and UI storyboards

Testing and QA - Functional testing and UAT execution

## 🚀 How Could It Be Improved
Features to Add/Enhance
Short-term Improvements
□ PDF Report Generation - Export visitor reports in PDF format
□ Push Notifications - Real-time alerts for new registrations
□ Enhanced QR Codes - Auto-time recording when QR is scanned
□ Mobile Application - Dedicated mobile app for easier access
□ Visitor History - Track visitor history and patterns
□ Automated Check-out - Time-out automation for visitors
Long-term Features
□ Multi-language Support - Bahasa Malaysia and English
□ Advanced Analytics - Predictive analytics for visitor patterns
□ Visitor Feedback System - Post-visit satisfaction surveys
□ Integration with HR System - Employee database integration
□ E-Invitation System - Digital invitations with registration links
□ Enhanced Security - Two-factor authentication for administrators
□ Cloud Backup - Automated data backup and recovery
Technical Improvements
□ Penetration Testing - Security testing for external access deployment
□ Performance Optimization - Database indexing for faster searches
□ API Development - RESTful API for third-party integration
□ Caching Implementation - Improve dashboard loading times
□ Containerization - Docker deployment for easier setup
□ CI/CD Pipeline - Automated testing and deployment
□ Monitoring Tools - System performance and error tracking
User Experience Enhancements
□ UI Redesign - More modern and intuitive interface
□ Mobile Optimization - Better mobile responsiveness
□ Accessibility Features - Support for users with disabilities
□ Dark Mode - User preference for dark theme
□ Interactive Dashboard - Clickable charts and graphs
□ Guided Tour - First-time user walkthrough
## 📲 How to Install It
### Prerequisites
XAMPP/WAMP/LAMP - Local development environment

PHP 7.4 or later - PHP runtime

MySQL 5.7 or later - Database server

Apache 2.4 or later - Web server

Browser - Google Chrome or Microsoft Edge

## Project File Installation
### Method 1: Source Code Installation
Step 1: Clone Repository

bash
git clone https://github.com/yourusername/ipelawat.git
cd ipelawat
Step 2: Configure Web Server

Place project in your web server root directory:

XAMPP: C:\xampp\htdocs\myproject

WAMP: C:\wamp64\www\myproject

Linux: /var/www/html/myproject

Update Apache configuration if needed:

apache
DocumentRoot "C:/xampp/htdocs/myproject/public"
<Directory "C:/xampp/htdocs/myproject/public">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>

if installing using xampp:

DocumentRoot "C:\xampp/htdocs\"
<Directory "C:\xampp\htdocs\">

find the httpd.conf file and replace the document root and directory with below:


DocumentRoot "C:/xampp/htdocs/myproject/public"
<Directory "C:/xampp/htdocs/myproject/public">

if installing using laragon:

DocumentRoot "C:\laragon\www"
<Directory "C:\laragon\www">

find the httpd.conf file and replace the document root and directory above with below:

DocumentRoot "C:\laragon\www\myproject\public"
<Directory "C:\laragon\www\myproject\public">

Step 3: Database Setup

sql
-- Create database
CREATE DATABASE ipelawat_db;
USE ipelawat_db;

-- Import database
SOURCE database/ipelawat_db.sql;
(the collation format is in utf8)

Step 4: Configure Application

Copy .env.example to .env

Update database configuration in .env:

env
# Database Configuration
database.default.hostname = localhost
database.default.database = ipelawat_db
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
Configure base URL:

env
app.baseURL = 'http://localhost/myproject/'
Step 5: Set Permissions

bash
# Linux/Mac
chmod -R 755 writable/
chmod -R 755 public/uploads/

# Windows (through File Explorer)
# Right-click writable/ and public/uploads/ folders
# Properties > Security > Set write permissions
Step 6: Run the Application

Start Apache and MySQL services

Access: 
http://localhost/pelawat (for visitors)
http://localhost/educated (for admin)

### Method 2: Direct Download Installation
Download Package

Download the project ZIP file

Extract to web server root directory

Follow Steps 3-6 from Method 1

Troubleshooting
Issue	Solution
Database Connection Error	Check credentials in .env file
404 Not Found	Verify base URL and Apache mod_rewrite is enabled
Write Permission Errors	Set proper permissions on writable/ directory
PHP Extensions Missing	Enable mbstring, pdo_mysql, mysql extensions in php.ini
Intranet Access Only	Configure network settings and firewall rules
Default Administrator Access

Email: admin@ipelawat.com

Password: admin123

Note: Change default credentials immediately after first login

📷 System Screenshots
Landing Page
Clean and modern interface for system access

Dashboard
Comprehensive visitor statistics and analytics

Registration Form
User-friendly registration interface with QR code generation

Calendar View
Visual calendar for visitor record management

🎥 Video Demo
[Demo video link to be added]

Demo Highlights:

✅ System login and authentication

✅ Dashboard navigation and statistics

✅ Visitor registration process

✅ QR code generation and scanning

✅ WhatsApp link sharing

✅ Manual registration by administrators

✅ Calendar search functionality

✅ Profile management

✅ Administrator management (SuperAdmin only)

👥 Contributors
Name	Role
Alif Firdaus Azhar	Lead Developer
Ms Nurul Elliza Jasmin	Project Supervisor

📄 License
This project is developed for educational and organizational purposes as part of the internship program at Bahagian Pengurusan Maklumat, Pejabat Setiausaha Kerajaan Negeri Selangor.

🙏 Acknowledgments
Bahagian Pengurusan Maklumat, SUK Selangor - Project opportunity and support

All stakeholders - Requirements gathering and feedback

Open source community - Tools and libraries used

Internship supervisors - Guidance and mentorship

© 2026 Bahagian Pengurusan Maklumat, SUK Selangor

"Mengurus Pelawat, Memudahkan Urusan"

(back to top)
