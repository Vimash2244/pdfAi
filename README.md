# PDF Parse AI

A powerful Laravel-based application that uses AI models (OpenAI GPT-4 and Google Gemini Pro) to parse PDF documents and extract structured information. Built with Vue 3, TypeScript, and Inertia.js.

## Features

### 🚀 Core Functionality
- **AI-Powered PDF Parsing**: Extract structured data from PDFs using OpenAI GPT-4 and Google Gemini Pro
- **Multiple AI Models**: Support for both OpenAI and Gemini AI models
- **JSON Output**: Get structured, machine-readable data from your documents
- **File Management**: Secure file upload and storage system

### 👥 User Management
- **Role-Based Access**: Super Admin and User roles
- **Subscription System**: Tiered subscription plans (Basic, Pro, Enterprise)
- **API Key Management**: Generate and manage API keys for external integrations

### 🔧 Admin Features (Super Users)
- **User Management**: View, edit, and manage user accounts
- **Subscription Management**: Assign and revoke user subscriptions
- **AI Model Configuration**: Configure and manage AI model settings
- **System Monitoring**: Track usage and system performance

### 🌐 API Integration
- **RESTful API**: Simple and intuitive API endpoints
- **Authentication**: Secure API key-based authentication
- **Rate Limiting**: Built-in rate limiting based on subscription plans
- **Comprehensive Documentation**: Detailed API documentation with examples

## Technology Stack

- **Backend**: Laravel 11, PHP 8.2+
- **Frontend**: Vue 3, TypeScript, Inertia.js
- **UI Components**: shadcn-vue
- **Database**: MySQL/PostgreSQL
- **AI Models**: OpenAI GPT-4, Google Gemini Pro
- **File Storage**: Local filesystem (configurable for S3)

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- MySQL/PostgreSQL database
- OpenAI API key
- Google Gemini API key

### 1. Clone the Repository
```bash
git clone https://github.com/yourusername/pdfparseai.git
cd pdfparseai
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install Node.js Dependencies
```bash
npm install
```

### 4. Environment Configuration
Copy the environment file and configure your settings:
```bash
cp .env.example .env
```

Edit `.env` file with your configuration:
```env
APP_NAME="PDF Parse AI"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pdfparseai
DB_USERNAME=root
DB_PASSWORD=your_password

OPENAI_API_KEY=your_openai_api_key
GEMINI_API_KEY=your_gemini_api_key
```

### 5. Generate Application Key
```bash
php artisan key:generate
```

### 6. Run Database Migrations
```bash
php artisan migrate
```

### 7. Seed the Database
```bash
php artisan db:seed
```

### 8. Build Frontend Assets
```bash
npm run build
```

### 9. Start the Development Server
```bash
php artisan serve
```

## Default Accounts

After seeding, you'll have these default accounts:

- **Super Admin**: admin@pdfparseai.com / password
- **Test User**: user@pdfparseai.com / password

## Usage

### Web Interface
1. Visit `http://localhost:8000`
2. Login with your credentials
3. Navigate to "PDF Parse" to upload and parse documents
4. View results and manage your parse history

### API Integration
The API provides two main endpoints:

#### Parse PDF
```bash
POST /api/pdf-parse
Headers:
  X-API-Key: your_public_key
  X-API-Secret: your_secret_key
Body:
  pdf_file: [PDF file]
  ai_model: openai (optional)
```

#### Check Status
```bash
GET /api/pdf-parse/{id}/status
Headers:
  X-API-Key: your_public_key
  X-API-Secret: your_secret_key
```

For complete API documentation, see `public/api-documentation.md`.

## Subscription Plans

| Plan | Price | API Calls | PDF Size | Features |
|------|-------|-----------|----------|----------|
| Basic | $9.99/month | 100/month | 5MB | Basic parsing, email support |
| Pro | $29.99/month | 1,000/month | 25MB | Advanced parsing, priority support, model selection |
| Enterprise | $99.99/month | Unlimited | 100MB | Full features, 24/7 support, custom integrations |

## Configuration

### AI Models
Configure AI models in the admin panel or directly in the database:

```php
// Example AI Model Configuration
[
    'name' => 'OpenAI',
    'model_identifier' => 'gpt-4',
    'config' => [
        'api_key' => env('OPENAI_API_KEY'),
        'endpoint' => 'https://api.openai.com/v1/chat/completions'
    ]
]
```

### File Storage
Configure file storage in `config/filesystems.php`:

```php
'disks' => [
    'local' => [
        'driver' => 'local',
        'root' => storage_path('app'),
    ],
    's3' => [
        'driver' => 's3',
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION'),
        'bucket' => env('AWS_BUCKET'),
    ],
],
```

## Development

### Running Tests
```bash
php artisan test
```

### Code Quality
```bash
# PHP CS Fixer
./vendor/bin/php-cs-fixer fix

# PHPStan
./vendor/bin/phpstan analyse
```

### Frontend Development
```bash
# Watch for changes
npm run dev

# Build for production
npm run build
```

## Deployment

### Production Requirements
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL/PostgreSQL
- Redis (recommended for caching)
- Queue worker (for background processing)

### Deployment Steps
1. Set `APP_ENV=production` in `.env`
2. Run `npm run build` to compile assets
3. Set up queue worker: `php artisan queue:work`
4. Configure web server (Nginx/Apache)
5. Set up SSL certificates
6. Configure file storage (S3 recommended for production)

## Security

- API key-based authentication
- Rate limiting per subscription plan
- File type validation
- SQL injection protection
- XSS protection
- CSRF protection

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

- **Documentation**: [API Documentation](public/api-documentation.md)
- **Issues**: [GitHub Issues](https://github.com/yourusername/pdfparseai/issues)
- **Email**: support@pdfparseai.com

## Changelog

### v1.0.0
- Initial release
- OpenAI GPT-4 integration
- Google Gemini Pro integration
- Basic subscription system
- Web interface
- RESTful API

## Roadmap

- [ ] Advanced PDF parsing options
- [ ] Batch processing
- [ ] Custom AI model training
- [ ] Webhook support
- [ ] Advanced analytics dashboard
- [ ] Multi-language support
- [ ] Mobile app
- [ ] Enterprise SSO integration
