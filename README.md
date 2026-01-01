# PHP_Laravel12_Generate_And_Read_Sitemap_XML

---


## Project Explanation:

### What is Sitemap XML?

A sitemap.xml is a special XML file that tells Google / search engines:

Which URLs exist on your website

When a page was last updated

How frequently pages change

Which pages are important

This helps SEO (Search Engine Optimization).


## What You’ll Build

Generate a dynamic sitemap.xml file
Read the sitemap in browser
Store sitemap links from database (Posts table)
Use proper routes, controller, view, headers


---

# Project SetUp

---

## STEP 1: Create New Laravel 12 Project

### Run Command :

```
composer create-project laravel/laravel PHP_Laravel12_Generate_And_Read_Sitemap_XML "12.*"

```

### Go inside project:

```
cd PHP_Laravel12_Generate_And_Read_Sitemap_XML

```

Make sure Laravel 12 installed successfully.



## STEP 2: Database Configuration

### Open .env file and update database credentials:

```

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel12_sitemap
DB_USERNAME=root
DB_PASSWORD=

```

### Create database:

```
laravel12_sitemap

```


## Step 3: Create Posts Table

### Run Command :

```

php artisan make:migration create_posts_table


```



### Update Migration File

File: database/migrations/<timestamp>_create_posts_table.php

```

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('body')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};

```

### Run migration:
```

php artisan migrate

```


## Step 4: Create Model + Factory (Dummy Data)


### Run Command :

```
php artisan make:model Post -f

```

### ﻿Update Factory: database/factories/PostFactory.php

```

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(),
            'slug' => Str::slug($this->faker->unique()->sentence()),
            'body' => $this->faker->paragraph(),
        ];
    }
}

```


### Model: app/Models/Post.php

```

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    // Table name (optional, Laravel auto-detects)
    protected $table = 'posts';

    // Mass assignable fields
    protected $fillable = [
        'title',
        'slug',
        'body',
    ];
}

```

### Create Dummy Posts

Write this:

```

php artisan tinker
App\Models\Post::factory()->count(20)->create();
exit

```


## Step 5: Create Controller

### Run Command :

```
php artisan make:controller SitemapController

```


### Open app/Http/Controllers/SitemapController.php and update:

```

<?php
namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    public function index()
    {
        // Fetch posts from DB
        $posts = Post::orderBy('updated_at','DESC')->get();

        // Show sitemap view
        return response()->view('sitemap.index', compact('posts'))
                         ->header('Content-Type', 'application/xml');
                        
    }
}

```


## Step 6: Create Sitemap View (XML)


### File: resources/views/sitemap/index.blade.php

```

{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    @foreach ($posts as $post)
        <url>
            <loc>{{ url('/post/'.$post->slug) }}</loc>
            <lastmod>{{ $post->updated_at->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach

</urlset>


```



## Step 7: Define Routes

### Open routes/web.php and add:

```
use App\Http\Controllers\SitemapController;

Route::get('/sitemap.xml', [SitemapController::class, 'index']);

```


## Step 8: Test in Browser

### Run Laravel server:

```
php artisan serve

```

### Open in browser:

```
http://127.0.0.1:8000/sitemap.xml

```

### You will see this type Output:


<img width="1918" height="969" alt="Screenshot 2026-01-01 115223" src="https://github.com/user-attachments/assets/6adb29f5-a515-4861-8c78-5390ca1d757c" />


---


# Project Folder Structure

```

PHP_Laravel12_Generate_And_Read_Sitemap_XML
│
├── app
│   ├── Http
│   │   └── Controllers
│   │       └── SitemapController.php   
│   │
│   └── Models
│       └── Post.php                    
│
├── database
│   ├── factories
│   │   └── PostFactory.php             
│   │
│   ├── migrations
│   │   └── 2025_xx_xx_xxxxxx_create_posts_table.php  
│   │
│   └── seeders
│       └── DatabaseSeeder.php
│
├── resources
│   └── views
│       └── sitemap
│           └── index.blade.php          
│
├── routes
│   └── web.php                          
│
├── .env                                 
├── artisan
├── composer.json
├── package.json
├── public
│   └── index.php
└── vendor

```
