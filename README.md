# Mailifica PHP SDK (`mailifica-php`)

> SDK oficial em PHP para a infraestrutura de e-mails transacionais **Mailifica**, com interface fluida e compatível com o Resend PHP SDK.

---

## 📦 Instalação

Instale o pacote via [Composer](https://getcomposer.org):

```bash
composer require mailifica/mailifica-php
```

---

## 🚀 Como Usar

### 1. Inicializar o Cliente

```php
use Mailifica\Mailifica;

// Inicialização com a chave de API
$mailifica = new Mailifica('ma_live_123456789');

// Ou através do método estático auxiliar (lê MAILIFICA_API_KEY se omitido):
$mailifica = Mailifica::client();
```

---

### 2. Enviar um E-mail Transacional

```php
$response = $mailifica->emails->send([
    'from' => 'onboarding@suaempresa.ao',
    'to' => ['cliente@gmail.com'],
    'subject' => 'Boas-vindas à Mailifica!',
    'html' => '<h1>Olá!</h1><p>Seu e-mail transacional foi entregue com sucesso.</p>',
]);

echo 'E-mail enviado! ID: ' . $response['id'];
```

---

### 3. Envio em Lote (Batch)

```php
$response = $mailifica->batch->send([
    [
        'from' => 'news@suaempresa.ao',
        'to' => 'cliente1@gmail.com',
        'subject' => 'Atualização Semanal #1',
        'html' => '<p>Conteúdo da newsletter 1</p>',
    ],
    [
        'from' => 'news@suaempresa.ao',
        'to' => 'cliente2@gmail.com',
        'subject' => 'Atualização Semanal #2',
        'html' => '<p>Conteúdo da newsletter 2</p>',
    ],
]);

print_r($response);
```

---

### 4. Gestão de Domínios & API Keys

```php
// Criar novo domínio
$domain = $mailifica->domains->create([
    'name' => 'suaempresa.ao',
]);

// Verificar registros DNS
$verified = $mailifica->domains->verify($domain['id']);

// Listar API Keys
$apiKeys = $mailifica->apiKeys->list();
```

---

### 5. Validação de Webhooks HMAC SHA-256

```php
use Mailifica\Services\Webhooks;

$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_MAILIFICA_SIGNATURE'] ?? '';
$secret = getenv('MAILIFICA_WEBHOOK_SECRET');

// Validar assinatura
$isValid = Webhooks::verifySignature($payload, $signature, $secret);

if (!$isValid) {
    http_response_code(400);
    exit('Invalid signature');
}

// Obter dados do evento
$event = Webhooks::constructEvent($payload, $signature, $secret);
echo 'Evento recebido: ' . $event['event'];
```

---

## 🧪 Testes

Para rodar os testes unitários do SDK:

```bash
composer test
# ou
vendor/bin/phpunit
```

---

## 📄 Licença

Distribuído sob a licença **MIT**. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

Copyright (c) 2026 [Mailifica](https://mailifica.com).
