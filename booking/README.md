# Online Lesson Booking System

A comprehensive Laravel-based online lesson booking system designed for educational institutions, tutoring services, and individual teachers.

## 🚀 Features

### Core Functionality
- **Multi-role System**: Admin, Teacher, and Student roles with distinct permissions
- **Real-time Booking**: Live availability checking and instant booking confirmation
- **Zoom Integration**: Seamless video conferencing with automatic meeting creation
- **Payment Processing**: Stripe integration for secure payment handling
- **Session Recording**: Automatic recording and playback functionality

### User Experience
- **Responsive Design**: Mobile-first approach with modern UI/UX
- **Real-time Notifications**: Email and in-app notifications
- **Multi-language Support**: Built-in internationalization
- **Theme Customization**: Flexible theming system

### Admin Features
- **Comprehensive Dashboard**: Analytics, reports, and system overview
- **User Management**: Complete user lifecycle management
- **Booking Oversight**: Monitor and manage all bookings
- **System Settings**: Configure application-wide settings

### Teacher Features
- **Availability Management**: Set and manage teaching schedules
- **Student Management**: Track student progress and feedback
- **Session Tools**: Recording, notes, and assessment tools
- **Earnings Tracking**: Monitor payments and earnings

### Student Features
- **Easy Booking**: Simple booking interface with calendar integration
- **Session Access**: Join Zoom meetings with one click
- **Progress Tracking**: View session history and recordings
- **Feedback System**: Rate and review teachers

## 🛠️ Technical Specifications

- **Framework**: Laravel 11.x
- **Database**: MySQL 5.7+
- **Frontend**: Bootstrap 5, jQuery, Alpine.js
- **Video**: Zoom API integration
- **Payments**: Stripe API
- **Email**: Laravel Mail with SMTP support

## 📋 Requirements

- PHP 8.1 or higher
- MySQL 5.7 or higher
- Composer
- Node.js & NPM
- Web server (Apache/Nginx)

## 🔧 Installation

1. **Upload Files**: Extract and upload to your web server
2. **Install Dependencies**: `composer install --optimize-autoloader --no-dev`
3. **Environment Setup**: Copy `.env.example` to `.env` and configure
4. **Generate Key**: `php artisan key:generate`
5. **Database Setup**: `php artisan migrate --force`
6. **Seed Data**: `php artisan db:seed --force`
7. **Storage Link**: `php artisan storage:link`

## 🔑 Default Credentials

- **Admin**: admin@example.com / password
- **Teacher**: john.smith@example.com / password  
- **Student**: mike.wilson@example.com / password

## 🔌 Integrations

### Zoom
- Automatic meeting creation
- Recording management
- Passcode protection
- Meeting analytics

### Stripe
- Secure payment processing
- Subscription management
- Invoice generation
- Payment tracking

### Email
- SMTP configuration
- Template system
- Automated notifications
- Multi-language support

## 📱 Mobile Responsive

Fully responsive design that works perfectly on:
- Desktop computers
- Tablets
- Mobile phones
- Various screen sizes

## 🎨 Customization

- **Theme System**: Easy color and layout customization
- **Email Templates**: Customizable email notifications
- **User Interface**: Flexible component system
- **Database**: Extensible schema for custom fields

## 📊 Analytics & Reporting

- Booking statistics
- Revenue tracking
- User engagement metrics
- Performance analytics
- Custom report generation

## 🔒 Security Features

- CSRF protection
- SQL injection prevention
- XSS protection
- Secure authentication
- Role-based permissions
- Data encryption

## 📞 Support

For technical support and customization requests, please contact us through ThemeForest.

## 📄 License

This project is licensed under the ThemeForest Regular License. See LICENSE file for details.

## 🔄 Updates

Regular updates include:
- Security patches
- Feature enhancements
- Performance improvements
- Bug fixes
- New integrations

---

**Ready to launch your online teaching platform? Get started with this comprehensive booking system today!**
