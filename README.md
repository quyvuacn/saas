# VTI ES IVM Frontend

## Directory Structure

```
.
├── docker/                  # Docker configuration files
│   ├── nginx/              # Nginx configuration
│   └── php/                # PHP configuration
├── manifests/              # Kubernetes manifests
│   └── vti-es-ivm/        # Application manifests
│       ├── configmap.yaml  # Application configuration
│       ├── deployment.yaml # Deployment configuration
│       ├── ingress.yaml    # Ingress configuration
│       ├── namespace.yaml  # Namespace definition
│       ├── pvc.yaml        # Persistent Volume Claim
│       ├── secret.yaml     # Sensitive data
│       ├── service.yaml    # Service definition
│       └── php-config.yaml # PHP configuration
├── Dockerfile              # Docker image definition
└── docker-compose.yml      # Docker Compose configuration
```

## Prerequisites

- Docker
- Docker Compose
- kubectl
- kustomize

## Local Development with Docker Compose

1. Build and run containers:
```bash
docker-compose up -d
```

2. Check status:
```bash
docker-compose ps
```

3. View logs:
```bash
docker-compose logs -f
```

4. Stop containers:
```bash
docker-compose down
```

## Deploy to Kubernetes

1. Build Docker image:
```bash
docker build -t vti-es-ivm:latest .
```

2. Deploy to Kubernetes:
```bash
kubectl apply -k manifests/vti-es-ivm
```

3. Check deployment status:
```bash
kubectl get pods -n vti-es-ivm
```

4. View logs:
```bash
kubectl logs -n vti-es-ivm -l app=vti-es-ivm
```

5. Check services:
```bash
kubectl get svc -n vti-es-ivm
```

6. Check ingress:
```bash
kubectl get ingress -n vti-es-ivm
```

## Configuration

### Docker
- PHP configuration: `docker/php/php.ini`
- Nginx configuration: `docker/nginx/conf.d/app.conf`

### Kubernetes
- Application config: `manifests/vti-es-ivm/configmap.yaml`
- PHP config: `manifests/vti-es-ivm/php-config.yaml`
- Secrets: `manifests/vti-es-ivm/secret.yaml`

## Troubleshooting

1. Check pod logs:
```bash
kubectl logs -n vti-es-ivm <pod-name>
```

2. Check namespace events:
```bash
kubectl get events -n vti-es-ivm
```

3. Check deployment details:
```bash
kubectl describe deployment -n vti-es-ivm vti-es-ivm
```

4. Check pod details:
```bash
kubectl describe pod -n vti-es-ivm <pod-name>
```

## Cleanup

1. Delete deployment:
```bash
kubectl delete -k manifests/vti-es-ivm
```

2. Delete namespace:
```bash
kubectl delete namespace vti-es-ivm
```

## Notes

- Ensure secrets are properly configured before deployment
- Verify resource limits match environment requirements
- Backup data before deleting PVC

## Development Environment Setup

### Prerequisites

- Docker
- Docker Compose
- VS Code
- VS Code Remote - Containers extension

### Getting Started

1. Clone the repository:
```bash
git clone <repository-url>
cd vti-es-ivm-frontend
```

2. Copy .env.example to .env:
```bash
cp .env.example .env
```

3. Open in VS Code with Dev Containers:
- Install VS Code Remote - Containers extension
- Open the project in VS Code
- Click "Reopen in Container" when prompted

4. Install dependencies:
```bash
composer install
npm install
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Run migrations:
```bash
php artisan migrate --seed
```

7. Start the development server:
```bash
php artisan serve
```

The application will be available at http://localhost:8000

### Debugging

- Xdebug is configured and ready to use
- Set breakpoints in your code
- Start debugging session in VS Code
- The debugger will automatically attach when you make requests

### Database

- MySQL is running on port 3307
- Database: laravel
- Username: laravel
- Password: laravel

### Additional Commands

- Run tests: `php artisan test`
- Clear cache: `php artisan cache:clear`
- Clear config: `php artisan config:clear`
- Clear route cache: `php artisan route:clear`
- Clear view cache: `php artisan view:clear`

### Troubleshooting

If you encounter any issues:

1. Rebuild containers:
```bash
docker-compose down
docker-compose up -d --build
```

2. Check container logs:
```bash
docker-compose logs
```

3. Check specific service logs:
```bash
docker-compose logs app
docker-compose logs nginx
docker-compose logs mysql
```
