# Biogenix Inc. Pvt. Ltd. - B2B eCommerce Portal

A comprehensive B2B web application built for Biogenix Inc. Pvt. Ltd. to streamline the distribution, cataloging, and sales of medical and diagnostic equipment. Built on modern web technologies, it features a custom Proforma Invoice (PI) generator, an interactive product catalog, and an integrated cart & checkout system tailored for B2B operations.

---

## 🚀 Key Features

* **Dynamic Product Catalog:** Categorized listing of medical devices, reagents, and diagnostic tools with advanced search and filtering.
* **Proforma Invoice (PI) Generator:** A highly interactive, client-side calculator for generating custom Pi Quotations. Includes dynamic row additions, interactive modals, GST/Freight calculations, Indian Rupee word conversion, and PDF export capabilities.
* **B2B User Management:** Custom profile management for B2B clients, handled securely via Laravel Fortify.
* **Cart & Checkout Layout:** Dedicated cart sidebar and checkout flows designed to minimize friction for bulk wholesale orders.
* **Responsive Modern UI:** Premium aesthetic utilizing Tailwind CSS v4, guaranteeing a fluid experience across mobile, tablet, and desktop viewports.

---

## 🛠️ Technology Stack

**Backend**
* **Framework:** [Laravel 12.x](https://laravel.com/) (PHP ^8.2)
* **Authentication:** Laravel Fortify
* **PDF Generation:** Barryvdh Laravel DOMPDF
* **Database:** MySQL

**Frontend**
* **Styling:** [Tailwind CSS v4](https://tailwindcss.com/)
* **Bundler:** [Vite 7](https://vitejs.dev/)
* **Interactivity:** Vanilla JavaScript strictly integrated within Blade templates for high-performance DOM manipulation.

---

## 🧰 Prerequisites

Before getting started, ensure your environment meets the following requirements:
* PHP >= 8.2
* Composer >= 2.x
* Node.js >= 18.x and npm >= 9.x
* MySQL (or equivalent relational database)

---

## ⚙️ Installation & Setup

Follow these steps to set up the project locally:

1. **Clone the repository**
   ```bash
   git clone https://github.com/pravinDatir/biogenix-website.git
   cd biogenix-website
   ```

2. **Install PHP Dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js Dependencies**
   ```bash
   npm install
   ```

4. **Environment Configuration**
   Copy the example environment file and generate your application key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Note: Open `.env` and configure your `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` settings.*

5. **Run Database Migrations**
   ```bash
   php artisan migrate
   ```

6. **Compile Frontend Assets**
   Build the Tailwind CSS styling and frontend scripts via Vite:
   ```bash
   npm run build
   ```
   *For live development, run `npm run dev` instead.*

7. **Start the Local Development Server**
   ```bash
   php artisan serve
   ```
   The application will be accessible at `http://localhost:8000`.

---

## 📂 Key Directory Structure

* `/app` - Core business logic, Controllers (e.g., `ProformaInvoiceController`)
* `/resources/views` - Blade templates:
  * `/pi-quotation` - Custom PI Generator interface
  * `/partials` - Reusable components (Navbar, Cart Sidebar, Footer)
  * `/pages` - Core views (Checkout, Product Catalog)
* `/routes/web.php` - Map of application endpoints.
* `/public/build` - Compiled, production-ready frontend assets.

---

## 📄 License

This project is proprietary software developed for Biogenix Inc. Pvt. Ltd. All rights reserved.
