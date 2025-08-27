# PDF Parse AI API Documentation

## Overview
The PDF Parse AI API allows you to extract structured information from PDF documents using advanced AI models (OpenAI GPT-4 and Google Gemini Pro). This API is designed to be simple, reliable, and easy to integrate into your applications.

## Base URL
```
https://yourdomain.com/api
```

## Authentication
All API requests require authentication using API keys. You need to include two headers:

- `X-API-Key`: Your public API key
- `X-API-Secret`: Your secret API key

### Getting API Keys
1. Register an account on our platform
2. Go to your dashboard
3. Navigate to API Keys section
4. Create a new API key pair

## Endpoints

### 1. Parse PDF
**POST** `/pdf-parse`

Upload a PDF file and get structured information extracted by AI.

#### Request Headers
```
Content-Type: multipart/form-data
X-API-Key: your-public-key
X-API-Secret: your-secret-key
```

#### Request Body (Form Data)
- `pdf_file` (required): PDF file (max 10MB)
- `ai_model` (optional): AI model to use ('openai' or 'gemini'). If not specified, uses your preferred model or default.

#### Response
```json
{
  "success": true,
  "data": {
    "id": 123,
    "status": "completed",
    "result": {
      "raw_response": "AI response text...",
      "parsed_data": {
        "title": "Document Title",
        "author": "Author Name",
        "date": "2024-01-01",
        "summary": "Document summary...",
        "key_points": ["Point 1", "Point 2"]
      },
      "model_used": "gpt-4",
      "tokens_used": 1500
    },
    "ai_model": "openai",
    "processed_at": "2024-01-01T12:00:00Z"
  }
}
```

#### Error Response
```json
{
  "success": false,
  "error": "Error message description"
}
```

### 2. Check Parse Status
**GET** `/pdf-parse/{id}/status`

Check the status of a PDF parsing request.

#### Request Headers
```
X-API-Key: your-public-key
X-API-Secret: your-secret-key
```

#### Response
```json
{
  "success": true,
  "data": {
    "id": 123,
    "status": "completed",
    "result": {
      "parsed_data": {
        "title": "Document Title",
        "author": "Author Name"
      }
    },
    "error": null,
    "processed_at": "2024-01-01T12:00:00Z",
    "created_at": "2024-01-01T11:59:00Z"
  }
}
```

## Status Codes

| Status | Description |
|--------|-------------|
| `processing` | PDF is being processed by AI |
| `completed` | PDF processing completed successfully |
| `failed` | PDF processing failed |

## Error Codes

| HTTP Status | Error | Description |
|-------------|-------|-------------|
| 400 | Bad Request | Invalid request parameters |
| 401 | Unauthorized | Invalid or missing API keys |
| 403 | Forbidden | Active subscription required |
| 404 | Not Found | PDF parse record not found |
| 500 | Internal Server Error | Server error during processing |

## Rate Limits
- **Basic Plan**: 100 API calls per month
- **Pro Plan**: 1,000 API calls per month  
- **Enterprise Plan**: Unlimited API calls

## File Requirements
- **Format**: PDF only
- **Size**: Maximum 10MB (varies by subscription plan)
- **Content**: Text-based PDFs work best

## AI Models

### OpenAI GPT-4
- **Model**: gpt-4
- **Best for**: Complex documents, detailed analysis
- **Response time**: 2-5 seconds
- **Token usage**: Tracked and reported

### Google Gemini Pro
- **Model**: gemini-pro
- **Best for**: General purpose parsing, cost-effective
- **Response time**: 1-3 seconds
- **Token usage**: Not tracked

## Code Examples

### cURL
```bash
curl -X POST https://yourdomain.com/api/pdf-parse \
  -H "X-API-Key: your-public-key" \
  -H "X-API-Secret: your-secret-key" \
  -F "pdf_file=@document.pdf" \
  -F "ai_model=openai"
```

### JavaScript (Fetch)
```javascript
const formData = new FormData();
formData.append('pdf_file', pdfFile);
formData.append('ai_model', 'openai');

const response = await fetch('https://yourdomain.com/api/pdf-parse', {
  method: 'POST',
  headers: {
    'X-API-Key': 'your-public-key',
    'X-API-Secret': 'your-secret-key'
  },
  body: formData
});

const result = await response.json();
```

### Python (requests)
```python
import requests

files = {'pdf_file': open('document.pdf', 'rb')}
data = {'ai_model': 'openai'}
headers = {
    'X-API-Key': 'your-public-key',
    'X-API-Secret': 'your-secret-key'
}

response = requests.post(
    'https://yourdomain.com/api/pdf-parse',
    files=files,
    data=data,
    headers=headers
)

result = response.json()
```

### PHP
```php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://yourdomain.com/api/pdf-parse');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'pdf_file' => new CURLFile('document.pdf'),
    'ai_model' => 'openai'
]);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-API-Key: your-public-key',
    'X-API-Secret: your-secret-key'
]);

$response = curl_exec($ch);
$result = json_decode($response, true);
curl_close($ch);
```

## Best Practices

1. **Error Handling**: Always check the `success` field in responses
2. **File Validation**: Ensure PDFs are valid and within size limits
3. **Rate Limiting**: Monitor your API usage to stay within limits
4. **Async Processing**: For large files, use the status endpoint to check progress
5. **Model Selection**: Choose AI models based on your specific needs

## Support
- **Email**: support@pdfparseai.com
- **Documentation**: https://yourdomain.com/docs
- **Status Page**: https://status.pdfparseai.com

## Changelog
- **v1.0.0**: Initial release with OpenAI and Gemini support
- **v1.1.0**: Added status endpoint and improved error handling
- **v1.2.0**: Added rate limiting and subscription management
