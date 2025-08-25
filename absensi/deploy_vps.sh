#!/bin/bash

# =============================================================================
# SCRIPT DEPLOYMENT OTOMATIS APLIKASI ABSENSI PKL DI VPS
# =============================================================================
# Script ini akan menginstall dan mengkonfigurasi aplikasi secara otomatis
# Jalankan dengan: sudo bash deploy_vps.sh

set -e  # Stop script jika ada error

# Colors untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function untuk print dengan color
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Function untuk check apakah user adalah root
check_root() {
    if [[ $EUID -ne 0 ]]; then
        print_error "Script ini harus dijalankan sebagai root (sudo)"
        exit 1
    fi
}

# Function untuk update sistem
update_system() {
    print_status "Updating sistem..."
    apt update && apt upgrade -y
    print_success "Sistem berhasil diupdate"
}

# Function untuk install dependencies
install_dependencies() {
    print_status "Installing dependencies..."
    
    # Install LAMP stack
    apt install -y apache2 mysql-server php php-mysql php-curl php-gd php-json \
                   php-mbstring php-xml php-zip php-opcache unzip git curl wget
    
    # Install additional PHP extensions
    apt install -y php-bcmath php-intl php-soap php-zip
    
    print_success "Dependencies berhasil diinstall"
}

# Function untuk setup MySQL
setup_mysql() {
    print_status "Setting up MySQL..."
    
    # Secure MySQL installation
    mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'root_password_kuat_disini';"
    mysql -e "DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');"
    mysql -e "DELETE FROM mysql.user WHERE User='';"
    mysql -e "DROP DATABASE IF EXISTS test;"
    mysql -e "FLUSH PRIVILEGES;"
    
    # Buat database dan user
    mysql -e "CREATE DATABASE IF NOT EXISTS absensi_pkl CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    mysql -e "CREATE USER IF NOT EXISTS 'absensi_user'@'localhost' IDENTIFIED BY 'absensi_password_kuat_disini';"
    mysql -e "GRANT ALL PRIVILEGES ON absensi_pkl.* TO 'absensi_user'@'localhost';"
    mysql -e "FLUSH PRIVILEGES;"
    
    print_success "MySQL berhasil disetup"
}

# Function untuk setup Apache
setup_apache() {
    print_status "Setting up Apache..."
    
    # Enable mod_rewrite
    a2enmod rewrite
    a2enmod ssl
    a2enmod headers
    
    # Buat virtual host
    cat > /etc/apache2/sites-available/absensi-pkl.conf << 'EOF'
<VirtualHost *:80>
    ServerName localhost
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/absensi-pkl/public
    
    <Directory /var/www/absensi-pkl/public>
        AllowOverride All
        Require all granted
        Options Indexes FollowSymLinks
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/absensi-pkl_error.log
    CustomLog ${APACHE_LOG_DIR}/absensi-pkl_access.log combined
    
    # Security headers
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
</VirtualHost>
EOF
    
    # Disable default site dan enable absensi-pkl
    a2dissite 000-default.conf
    a2ensite absensi-pkl.conf
    
    # Restart Apache
    systemctl restart apache2
    
    print_success "Apache berhasil disetup"
}

# Function untuk setup PHP
setup_php() {
    print_status "Setting up PHP..."
    
    # Backup php.ini original
    cp /etc/php/8.1/apache2/php.ini /etc/php/8.1/apache2/php.ini.backup
    
    # Update php.ini settings
    sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 10M/' /etc/php/8.1/apache2/php.ini
    sed -i 's/post_max_size = 8M/post_max_size = 10M/' /etc/php/8.1/apache2/php.ini
    sed -i 's/max_execution_time = 30/max_execution_time = 300/' /etc/php/8.1/apache2/php.ini
    sed -i 's/memory_limit = 128M/memory_limit = 256M/' /etc/php/8.1/apache2/php.ini
    sed -i 's/;date.timezone =/date.timezone = Asia\/Jakarta/' /etc/php/8.1/apache2/php.ini
    
    # Enable OPcache
    echo "opcache.enable=1" >> /etc/php/8.1/apache2/php.ini
    echo "opcache.memory_consumption=128" >> /etc/php/8.1/apache2/php.ini
    echo "opcache.interned_strings_buffer=8" >> /etc/php/8.1/apache2/php.ini
    echo "opcache.max_accelerated_files=4000" >> /etc/php/8.1/apache2/php.ini
    echo "opcache.revalidate_freq=2" >> /etc/php/8.1/apache2/php.ini
    echo "opcache.fast_shutdown=1" >> /etc/php/8.1/apache2/php.ini
    
    # Restart Apache
    systemctl restart apache2
    
    print_success "PHP berhasil disetup"
}

# Function untuk deploy aplikasi
deploy_app() {
    print_status "Deploying aplikasi..."
    
    # Buat direktori aplikasi
    mkdir -p /var/www/absensi-pkl
    
    # Clone repository (ganti dengan URL repository Anda)
    # cd /var/www
    # git clone https://github.com/username/absensi-pkl.git
    
    # Atau copy file manual (jika sudah ada)
    if [ -d "./app" ]; then
        cp -r . /var/www/absensi-pkl/
    else
        print_warning "Folder app tidak ditemukan. Silakan copy file manual ke /var/www/absensi-pkl/"
    fi
    
    # Set ownership dan permission
    chown -R www-data:www-data /var/www/absensi-pkl
    chmod -R 755 /var/www/absensi-pkl
    chmod 755 /var/www/absensi-pkl/public/uploads
    chmod 644 /var/www/absensi-pkl/public/uploads/.htaccess
    
    print_success "Aplikasi berhasil dideploy"
}

# Function untuk setup database schema
setup_database() {
    print_status "Setting up database schema..."
    
    # Import schema database
    if [ -f "/var/www/absensi-pkl/db_absensi_pkl.sql" ]; then
        mysql -u absensi_user -p'absensi_password_kuat_disini' absensi_pkl < /var/www/absensi-pkl/db_absensi_pkl.sql
        print_success "Database schema berhasil diimport"
    else
        print_warning "File db_absensi_pkl.sql tidak ditemukan"
    fi
    
    # Jalankan script tambah kolom
    if [ -f "/var/www/absensi-pkl/add_tempat_pkl_columns.sql" ]; then
        mysql -u absensi_user -p'absensi_password_kuat_disini' absensi_pkl < /var/www/absensi-pkl/add_tempat_pkl_columns.sql
        print_success "Kolom tambahan berhasil ditambahkan"
    else
        print_warning "File add_tempat_pkl_columns.sql tidak ditemukan"
    fi
}

# Function untuk setup firewall
setup_firewall() {
    print_status "Setting up firewall..."
    
    # Install UFW jika belum ada
    apt install -y ufw
    
    # Setup firewall rules
    ufw allow 22/tcp    # SSH
    ufw allow 80/tcp    # HTTP
    ufw allow 443/tcp   # HTTPS
    
    # Enable firewall
    ufw --force enable
    
    print_success "Firewall berhasil disetup"
}

# Function untuk setup SSL (Let's Encrypt)
setup_ssl() {
    print_status "Setting up SSL certificate..."
    
    # Install Certbot
    apt install -y certbot python3-certbot-apache
    
    print_warning "Untuk setup SSL, jalankan: certbot --apache -d yourdomain.com"
    print_warning "Pastikan domain sudah mengarah ke server ini"
}

# Function untuk setup backup
setup_backup() {
    print_status "Setting up backup system..."
    
    # Buat direktori backup
    mkdir -p /backup/absensi-pkl
    
    # Buat script backup
    cat > /usr/local/bin/backup-absensi.sh << 'EOF'
#!/bin/bash
BACKUP_DIR="/backup/absensi-pkl"
DATE=$(date +%Y%m%d_%H%M%S)

# Buat direktori backup
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u absensi_user -p'absensi_password_kuat_disini' absensi_pkl > $BACKUP_DIR/db_backup_$DATE.sql

# Backup aplikasi
tar -czf $BACKUP_DIR/app_backup_$DATE.tar.gz /var/www/absensi-pkl/

# Hapus backup lama (lebih dari 30 hari)
find $BACKUP_DIR -name "*.sql" -mtime +30 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +30 -delete

echo "Backup completed: $DATE" >> $BACKUP_DIR/backup.log
EOF
    
    # Set permission dan tambah ke crontab
    chmod +x /usr/local/bin/backup-absensi.sh
    
    # Tambah ke crontab (backup setiap hari jam 2 pagi)
    (crontab -l 2>/dev/null; echo "0 2 * * * /usr/local/bin/backup-absensi.sh") | crontab -
    
    print_success "Backup system berhasil disetup"
}

# Function untuk setup monitoring
setup_monitoring() {
    print_status "Setting up monitoring..."
    
    # Install monitoring tools
    apt install -y htop iotop nethogs
    
    # Buat script monitoring
    cat > /usr/local/bin/monitor-absensi.sh << 'EOF'
#!/bin/bash
echo "=== ABSENSI PKL SERVER MONITORING ==="
echo "Date: $(date)"
echo ""

echo "=== SYSTEM INFO ==="
echo "Uptime: $(uptime)"
echo "Load Average: $(cat /proc/loadavg)"
echo ""

echo "=== MEMORY USAGE ==="
free -h
echo ""

echo "=== DISK USAGE ==="
df -h
echo ""

echo "=== APACHE STATUS ==="
systemctl status apache2 --no-pager -l
echo ""

echo "=== MYSQL STATUS ==="
systemctl status mysql --no-pager -l
echo ""

echo "=== ACTIVE CONNECTIONS ==="
netstat -an | grep :80 | wc -l
echo "Active HTTP connections: $(netstat -an | grep :80 | wc -l)"
echo "Active HTTPS connections: $(netstat -an | grep :443 | wc -l)"
EOF
    
    chmod +x /usr/local/bin/monitor-absensi.sh
    
    print_success "Monitoring berhasil disetup"
}

# Function untuk test instalasi
test_installation() {
    print_status "Testing instalasi..."
    
    # Test Apache
    if systemctl is-active --quiet apache2; then
        print_success "Apache berjalan dengan baik"
    else
        print_error "Apache tidak berjalan"
    fi
    
    # Test MySQL
    if systemctl is-active --quiet mysql; then
        print_success "MySQL berjalan dengan baik"
    else
        print_error "MySQL tidak berjalan"
    fi
    
    # Test PHP
    if php -v > /dev/null 2>&1; then
        print_success "PHP berjalan dengan baik"
    else
        print_error "PHP tidak berjalan"
    fi
    
    # Test database connection
    if mysql -u absensi_user -p'absensi_password_kuat_disini' -e "USE absensi_pkl;" > /dev/null 2>&1; then
        print_success "Database connection berhasil"
    else
        print_error "Database connection gagal"
    fi
}

# Function untuk show summary
show_summary() {
    echo ""
    echo "============================================================================="
    echo "                    INSTALASI SELESAI! 🎉"
    echo "============================================================================="
    echo ""
    echo "📋 INFORMASI INSTALASI:"
    echo "   • Web Server: Apache2"
    echo "   • Database: MySQL"
    echo "   • PHP Version: $(php -r 'echo PHP_VERSION;')"
    echo "   • Document Root: /var/www/absensi-pkl/public"
    echo ""
    echo "🔐 CREDENTIALS DATABASE:"
    echo "   • Database: absensi_pkl"
    echo "   • Username: absensi_user"
    echo "   • Password: absensi_password_kuat_disini"
    echo "   • Root Password: root_password_kuat_disini"
    echo ""
    echo "🌐 WEB SERVER:"
    echo "   • HTTP: http://$(hostname -I | awk '{print $1}')"
    echo "   • Virtual Host: /etc/apache2/sites-available/absensi-pkl.conf"
    echo ""
    echo "📁 FOLDER PENTING:"
    echo "   • Aplikasi: /var/www/absensi-pkl"
    echo "   • Uploads: /var/www/absensi-pkl/public/uploads"
    echo "   • Logs: /var/log/apache2/"
    echo "   • Backup: /backup/absensi-pkl"
    echo ""
    echo "🔧 COMMANDS PENTING:"
    echo "   • Monitor: /usr/local/bin/monitor-absensi.sh"
    echo "   • Backup: /usr/local/bin/backup-absensi.sh"
    echo "   • Restart Apache: systemctl restart apache2"
    echo "   • Restart MySQL: systemctl restart mysql"
    echo ""
    echo "⚠️  PENTING:"
    echo "   • Ganti password database default!"
    echo "   • Setup SSL certificate dengan certbot"
    echo "   • Konfigurasi firewall sesuai kebutuhan"
    echo "   • Test aplikasi di browser"
    echo ""
    echo "============================================================================="
}

# Main function
main() {
    echo "============================================================================="
    echo "           SCRIPT DEPLOYMENT APLIKASI ABSENSI PKL DI VPS"
    echo "============================================================================="
    echo ""
    
    # Check root
    check_root
    
    # Update sistem
    update_system
    
    # Install dependencies
    install_dependencies
    
    # Setup MySQL
    setup_mysql
    
    # Setup Apache
    setup_apache
    
    # Setup PHP
    setup_php
    
    # Deploy aplikasi
    deploy_app
    
    # Setup database
    setup_database
    
    # Setup firewall
    setup_firewall
    
    # Setup SSL (optional)
    setup_ssl
    
    # Setup backup
    setup_backup
    
    # Setup monitoring
    setup_monitoring
    
    # Test instalasi
    test_installation
    
    # Show summary
    show_summary
}

# Run main function
main "$@"

