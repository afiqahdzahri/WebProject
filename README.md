Youtube Link: https://www.youtube.com/watch?v=mQFkFye8u14

<img width="920" height="469" alt="image" src="https://github.com/user-attachments/assets/c38bb6b5-d1d3-43dc-b40e-380648190f3f" />


# WebProject
This project presents the development of an automated system for monitoring  employee performance and managing feedback within a Business Process  Outsourcing (BPO) company, aimed at improving efficiency, transparency, and  employee engagement. 

#  For Users Login - : http://localhost/epes/login.php

# Installation Steps 
Step 1: Download XAMPP 
1. Open web browser and go to the official Apache Friends website: 
https://www.apachefriends.org/index.html 
2. Choose the version compatible with operating system (for Windows, click on the 
recommended version for OS bitness usually 64-bit). 
3. Click the download link. The installer file (e.g., xampp-windows-x64-8.2.12-0-VS16
installer.exe) will be saved to the computer. 
Step 2: Install XAMPP 
1. Locate the downloaded installer and double-click to run it. If prompted by User Account 
Control (UAC), click Yes. 
2. A security warning may appear. Click Yes or Run to proceed. 
3. In the setup wizard, click Next. 
4. Select Components: Ensure Apache, MySQL, PHP, and phpMyAdmin are selected. Click 
Next. 
5. Choose Install Location: 
○ It is recommended to use the default folder (C:\xampp). Avoid installing in 
C:\Program Files\ due to potential Windows permission issues. 
○ Click Next. 
6. The installer will now copy the files. This may take a few minutes. 
7. Completion: Once finished, leave the "Launch the XAMPP Control Panel" option checked 
and click Finish. 
79 
Step 3: Start the Services 
1. The XAMPP Control Panel will open. 
2. To start the web server and database server, click the Start button next to Apache and 
MySQL. 
3. If successful, the module names will turn green with a background, and the word "Running" 
will appear. 
Windows Firewall Alert: The first time you start Apache, Windows Defender Firewall will likely 
block it. You must click Allow access for both private and public networks for the servers to 
function correctly. 
Step 4: Verify the Installation 
1. Open the web browser. 
2. In the address bar, type: http://localhost or http://127.0.0.1 and press Enter. 
3. You should see the XAMPP Welcome/Dashboard page. This confirms that the Apache 
web server is running correctly. 
Step 5: Deploy Web Application 
The web application files must be placed in a specific folder for the server to find them. 
1. Extract the zip file after downloading the source code. 
2. Download or install any PHP-scripted local web server. 
3. Create a new database called "epes_db" by opening the web-server database. 
4. Bring in the SQL file from the source code's database folder. 
5. To access the local projects, copy and paste the source code to the location of the local web 
server. XAMPP('C:\xampp\htdocs') example 
6. Launch a browser and look through the project. For instance, [http://localhost/epes] 
7. Open the htdocs folder. This is the Document Root; it's where Apache looks for files to 
serve. 
8. For a simple application: Place all of the project files (e.g., index.php, style.css, folders) 
directly inside htdocs. 
9. To access the application in the browser: 
80 
○ If placed directly in htdocs: go to http://localhost/index.php 
Step 6: Import the Database (MySQL) 
If the application uses a database (like the epes_db from the SQL dump): 
1. In the browser, go to http://localhost/phpmyadmin/index.php 
2. Log in: 
a. Username: root (If asked) 
b. Password: (leave this blank by default) 
c. Click Go. 
3. Create a New Database: 
a. On the left sidebar, click New. 
b. Enter the database name (e.g., epes_db). 
c. Select collation utf8mb4_general_ci. 
d. Click Create. 
4. Import the SQL Dump File: 
a. Click on the newly created database name on the left. 
b. Click the Import tab at the top. 
c. Click Choose File and navigate to the sql dump file (the database folder file 
containing all the CREATE TABLE and INSERT statements). 
d. Leave the format as SQL. 
e. Click Go at the bottom. A success message will appear once the import is complete. 
