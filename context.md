# CONTEXT.md - Backend (Laravel API)

## Tema projekta: Veb prodavnica digitalnih proizvoda (slika)

Ovaj dokument sadrži plan razvoja i specifikacije za backend deo aplikacije. Svaki odeljak predstavlja jedan `git commit` sa jasnim zadacima.

**Tehnologije:**
* Backend: Laravel
* Autentifikacija: Laravel Sanctum
* Baza: MySQL (ili po izboru)
* Server-side manipulacija slikama: `intervention/image`

**Modeli podataka:**
1.  `User` (id, name, email, password, role)
2.  `Product` (id, name, description, price, original_file_path, preview_file_path)
3.  `Purchase` (id, user_id, product_id, purchase_timestamp)

---

### **Commit 1: Inicijalna postavka Laravel projekta**

**Zadatak:** Kreirati novu Laravel aplikaciju i napraviti prvi komit.

1.  Kreirati folder `backend`.
2.  Ući u `backend` folder.
3.  Pokrenuti komandu za kreiranje novog Laravel projekta unutar `backend` foldera:
    ```bash
    composer create-project laravel/laravel .
    ```

---

### **Commit 2: Konfiguracija baze i modifikacija User modela za uloge**

**Zadatak:** Podesiti konekciju sa bazom podataka i dodati sistem uloga (`admin`, `user`) na postojeći `User` model i migraciju.

1.  U `.env` fajlu, konfigurisati `DB_*` promenljive za konekciju sa vašom lokalnom bazom podataka.
2.  Modifikovati `database/migrations/xxxx_xx_xx_xxxxxx_create_users_table.php` da doda `role` kolonu sa `default('user')`.
3.  U modelu `app/Models/User.php`, dodati `role` u `$fillable` niz.

---

### **Commit 3: Kreiranje Product i Purchase modela i migracija**

**Zadatak:** Kreirati `Product` i `Purchase` modele sa pratećim migracijama i kontrolerima.

1.  Pokrenuti Artisan komande:
    ```bash
    php artisan make:model Product -mc
    php artisan make:model Purchase -m
    ```
2.  U migraciji za `products`, definisati kolone: `name`, `description`, `price`, `original_file_path`, `preview_file_path`.
3.  U migraciji za `purchases`, definisati spoljne ključeve: `user_id` i `product_id`.

---
### **Naredni koraci (skraćeno):**
- **Commit 4:** Definisanje Eloquent relacija u modelima.
- **Commit 5:** Instalacija Sanctum-a i kreiranje AuthController-a sa rutama za registraciju i login.
- **Commit 6:** Pokretanje svih migracija komandom `php artisan migrate`.
- **Commit 7:** Kreiranje Factory-ja i Seeder-a.
- **Commit 8:** Pokretanje servera sa `php artisan serve`.