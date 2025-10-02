<div align="center">
  <img src="https://media.appla-x.com/img/applax.png" alt="Applax Logo" width="200"/>
</div>

# Applax Payment Gateway Showcase

A comprehensive Laravel demonstration application showcasing the **applax-dev/gate-sdk** package capabilities. This interactive showcase demonstrates real payment processing, complete e-commerce workflows, and comprehensive SDK integrations.

## 🚀 Live Demo Features

### 🛍️ **Shop Demo**
Complete e-commerce experience with real payment processing:
- Product catalog with shopping cart
- Multi-step checkout process
- Real card payment integration
- Order confirmation and receipts

### 📊 **Admin Dashboard**
Professional merchant tools for payment management:
- Transaction monitoring and analytics
- Order management with capture/refund operations
- Customer database with Gateway synchronization
- Payment history and detailed reports
- Real-time order status updates

### 🔧 **SDK Showcase**
Interactive demonstrations of all Gate SDK APIs:
- **Products API** - CRUD operations with cursor pagination
- **Orders API** - Complete order lifecycle management
- **Clients API** - Customer profile management
- **Webhooks API** - Event notification system
- **Raw API Access** - Direct access to any Gateway endpoint

## ⚡ **New in v1.2.0: Raw API Access**

Unlock unlimited possibilities with direct access to any Applax Gate API endpoint:

```php
// Access new resources immediately
$brand = $gateSDK->rawPost('/brands/', [
    'name' => 'My Brand',
    'website' => 'https://mybrand.com'
]);

// Universal method for any endpoint
$response = $gateSDK->raw('GET', '/subscriptions/', null, ['limit' => 10]);
```

**Now Available:**
- 🏢 **Brands Management** - Create and manage brand profiles
- 🔄 **Subscriptions** - Recurring payment management
- 📊 **Taxes** - Tax calculation and management
- 💳 **Charges** - One-time payment processing

## 🛠️ **Technical Stack**

- **Framework:** Laravel 11
- **SDK:** applax-dev/gate-sdk v1.2.0
- **Frontend:** Bootstrap 5 with responsive design
- **Database:** MySQL with comprehensive migrations
- **Payment Processing:** Real Applax Gateway integration
- **Architecture:** Clean MVC with service layer

## 📋 **Key Features**

- ✅ **Real API Integration** - All demonstrations use live Gateway APIs
- ✅ **Complete Error Handling** - Comprehensive exception management
- ✅ **Professional UI/UX** - Modern, responsive design
- ✅ **Code Examples** - Copy-ready code snippets for every API call
- ✅ **Automated Cleanup** - Scheduled midnight cleanup of demo data
- ✅ **Comprehensive Testing** - Full payment flow validation

## 🎯 **Perfect For**

- **Developers** exploring Applax Gateway integration
- **Merchants** evaluating payment solutions
- **Technical Teams** understanding SDK capabilities
- **Product Demos** showcasing payment features
- **Integration Testing** validating payment flows

## 🔧 **Quick Setup**

```bash
# Clone and install
git clone <repository-url>
cd decta
composer install

# Configure environment
cp .env.example .env
# Add your Applax Gateway API credentials

# Setup database
php artisan migrate --seed
php artisan key:generate

# Start demo
php artisan serve
```

## 📚 **Documentation Links**

- [Gate SDK Documentation](https://github.com/applax-dev/gate-sdk/blob/master/docs/raw-api-access.md)
- [Applax API Reference](https://docs.appla-x.com/)
- [Gateway Dashboard](https://gate.appla-x.com/)

## 🧹 **Automated Maintenance**

The showcase includes automated cleanup that runs daily at midnight:
- Removes all demo orders, customers, and payments
- Cancels Gateway orders and deletes clients
- Resets database for fresh demonstrations
- Comprehensive error handling and logging

---

**Built with ❤️ to demonstrate the power of Applax Gateway SDK**

*This demo showcases real payment processing capabilities. All transactions are processed through the Applax Gateway using your API credentials.*
