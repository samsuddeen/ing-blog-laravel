# Laravel Blog API - Interview Task

## Overview
This is a RESTful Blog API built with Laravel, fulfilling the interview task requirements:
- User authentication with Sanctum (token-based)
- Role-based access control (Admin & Author)
- CRUD for Users, Posts, Categories, Tags, Comments
- Search & Filter functionality for Posts and Users
- Polymorphic relationships for Comments and Tags
- Dynamic pagination
- JSON API responses with resources
- Slug auto-generation
- Eager loading for optimization
- Input validation & robust error handling

---

## Setup Instructions

1. **Clone Repository**
```bash
git clone <repo link here>
cd <repo-folder>

2. Install Dependencies

composer install
npm install
npm run dev


3. Environment Setup

Copy .env.example to .env

Set database credentials

Set APP_URL=http://127.0.0.1:8000

Generate App Key

php artisan key:generate


5. Run Migrations & Seeders

php artisan migrate --seed

6. Run the Application

php artisan serve



## Extras Provided

- Database: Full database dump included  
- Postman Collection: few API endpoints pre-configured.



API Endpoints
Authentication

POST /api/v1/auth/register - Register a new user

POST /api/v1/auth/login - Login user

POST /api/v1/auth/logout - Logout user (auth required)

GET /api/v1/auth/me - Get authenticated user info

Users (Admin Only)

GET /api/v1/users - List users with search & filter

GET /api/v1/users/{id} - Show user

POST /api/v1/users - Create user

PUT /api/v1/users/{id} - Update user

DELETE /api/v1/users/{id} - Delete user

Posts

GET /api/v1/posts - List posts with search/filter (title, author, category, tags)

GET /api/v1/posts/{slug} - Show post

POST /api/v1/posts - Create post (auth required)

PUT /api/v1/posts/{id} - Update post

DELETE /api/v1/posts/{id} - Delete post

GET /api/v1/my-posts - Get posts created by authenticated user

POST /api/v1/posts/{id}/publish - Publish post

Categories

GET /api/v1/categories - List categories

GET /api/v1/categories/{slug} - Show category

CRUD endpoints (auth required)

Tags

GET /api/v1/tags - List tags

GET /api/v1/tags/{slug} - Show tag

CRUD endpoints (auth required)

Comments

GET /api/v1/comments - List comments

POST /api/v1/comments - Create comment

PUT /api/v1/comments/{id} - Update comment

DELETE /api/v1/comments/{id} - Delete comment

GET /api/v1/posts/{post_id}/comments - Comments for a post

Testing / Checking

Register / Login a user (Admin or Author).

Use Bearer Token in Authorization header for protected routes.

Test CRUD for Posts, Categories, Tags, Comments.

Search & Filter:

/api/v1/posts?search=Laravel

/api/v1/posts?author=Aalam

/api/v1/posts?category=Technology

/api/v1/posts?tag=PHP

Pagination: ?per_page=10&page=2
