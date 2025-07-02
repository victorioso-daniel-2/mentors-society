# 🔗 Database Relationships Diagram - Personal Access Tokens, Migrations & Social Media

## 📊 **Entity Relationship Overview**

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                           MENTORS SOCIETY DATABASE                              │
└─────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────────┐
│                              AUTHENTICATION LAYER                               │
└─────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────┐    ┌─────────────────────┐    ┌─────────────────────┐
│     USER TABLE      │    │  STUDENT TABLE      │    │ PERSONAL_ACCESS_    │
│                     │    │                     │    │     TOKENS          │
├─────────────────────┤    ├─────────────────────┤    ├─────────────────────┤
│ student_number (PK) │◄──►│ student_number (PK) │    │ id (PK)             │
│ password            │    │ last_name           │    │ tokenable_type      │
│ status              │    │ first_name          │    │ tokenable_id        │
│ created_at          │    │ middle_initial      │    │ name                │
│ updated_at          │    │ course              │    │ token               │
└─────────────────────┘    │ year_level          │    │ abilities           │
                           │ section             │    │ last_used_at        │
                           │ academic_status     │    │ expires_at          │
                           │ email               │    │ created_at          │
                           └─────────────────────┘    │ updated_at          │
                                                      └─────────────────────┘
                                                              │
                                                              │ Polymorphic
                                                              │ Relationship
                                                              ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│                              MIGRATION LAYER                                    │
└─────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────────┐
│ 2025_07_02_051241_alter_tokenable_id_on_personal_access_tokens_table.php      │
├─────────────────────────────────────────────────────────────────────────────────┤
│ BEFORE: tokenable_id BIGINT UNSIGNED                                           │
│ AFTER:  tokenable_id VARCHAR(255)                                              │
│                                                                                 │
│ Purpose: Enable polymorphic relationships for Laravel Sanctum                  │
│ Allows tokens to reference different entity types (User, Student, etc.)        │
└─────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────────┐
│                              BUSINESS LAYER                                     │
└─────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────┐    ┌─────────────────────┐    ┌─────────────────────┐
│   ROLE TABLE        │    │ PERMISSION TABLE    │    │ USER_ROLE TABLE     │
│                     │    │                     │    │                     │
├─────────────────────┤    ├─────────────────────┤    ├─────────────────────┤
│ role_id (PK)        │    │ permission_id (PK)  │    │ user_role_id (PK)   │
│ role_name           │    │ permission_name     │    │ student_number (FK) │
│ description         │    │ description         │    │ role_id (FK)        │
│ role_priority       │    └─────────────────────┘    │ academic_year_id(FK)│
└─────────────────────┘              │                │ start_date          │
         │                           │                │ end_date            │
         │                           │                └─────────────────────┘
         │                           │                         │
         │                           │                         │
         └───────────────────────────┼─────────────────────────┘
                                     │
                                     ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│                              FEATURE LAYER                                      │
└─────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────┐    ┌─────────────────────┐    ┌─────────────────────┐
│  SOCIAL_MEDIA       │    │     EVENT           │    │   TRANSACTION       │
│     TABLE           │    │     TABLE           │    │     TABLE           │
│                     │    │                     │    │                     │
├─────────────────────┤    ├─────────────────────┤    ├─────────────────────┤
│ social_media_id(PK) │    │ event_id (PK)       │    │ transaction_id (PK) │
│ platform            │    │ name                │    │ event_id (FK)       │
│ url                 │    │ description         │    │ type_id (FK)        │
└─────────────────────┘    │ event_date          │    │ amount              │
                           │ start_time          │    │ description         │
                           │ end_time            │    │ receipt_photo       │
                           │ venue               │    │ transaction_date    │
                           │ status_id (FK)      │    │ student_number (FK) │
                           │ created_by (FK)     │    │ verified_by (FK)    │
                           │ capacity            │    └─────────────────────┘
                           └─────────────────────┘
```

## 🔄 **Authentication Flow Diagram**

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   USER      │    │   LOGIN     │    │  PERSONAL   │    │   API       │
│  LOGIN      │───►│  REQUEST    │───►│   ACCESS    │───►│  ACCESS     │
│             │    │             │    │   TOKEN     │    │             │
└─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘
       │                   │                   │                   │
       ▼                   ▼                   ▼                   ▼
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│ Validate    │    │ POST        │    │ Store in    │    │ Bearer      │
│ Credentials │    │ /api/auth/  │    │ personal_   │    │ Token in    │
│             │    │ login       │    │ access_     │    │ Header      │
└─────────────┘    └─────────────┘    │ tokens      │    └─────────────┘
                                      └─────────────┘
```

## 🔗 **Relationship Types**

### **1. Direct Relationships**
```
USER ◄──► STUDENT (1:1)
- user.student_number = student.student_number

USER ◄──► PERSONAL_ACCESS_TOKENS (1:N)
- personal_access_tokens.tokenable_type = 'App\Models\User'
- personal_access_tokens.tokenable_id = user.student_number
```

### **2. Indirect Relationships**
```
USER ◄──► SOCIAL_MEDIA (N:N through API)
- No direct database relationship
- Access controlled through authentication and permissions
- Users can manage social media accounts via API endpoints
```

### **3. Migration Impact**
```
MIGRATION ◄──► PERSONAL_ACCESS_TOKENS
- Migration modifies tokenable_id column type
- Enables polymorphic relationships
- Supports different entity types for token authentication
```

## 📋 **API Endpoints Flow**

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                              API AUTHENTICATION FLOW                        │
└─────────────────────────────────────────────────────────────────────────────┘

1. LOGIN REQUEST
   POST /api/auth/login
   Body: { "student_number": "2021-0001", "password": "password" }
   
2. TOKEN GENERATION
   - Validate credentials against USER table
   - Create Personal Access Token
   - Store in personal_access_tokens table
   - Return token to client

3. API ACCESS
   GET /api/social-media/
   Header: Authorization: Bearer {token}
   
4. TOKEN VALIDATION
   - Check token in personal_access_tokens table
   - Verify tokenable_type = 'App\Models\User'
   - Verify tokenable_id = student_number
   - Check permissions for social media access

5. RESPONSE
   - Return social media data if authorized
   - Return 401 if unauthorized
```

## 🗂️ **Database Schema Relationships**

### **Personal Access Tokens Table**
```sql
CREATE TABLE personal_access_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,  -- 'App\Models\User'
    tokenable_id VARCHAR(255) NOT NULL,    -- student_number (e.g., '2021-0001')
    name VARCHAR(255) NOT NULL,            -- Token name
    token VARCHAR(64) NOT NULL UNIQUE,     -- Hashed token
    abilities TEXT NULL,                   -- Token permissions
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX personal_access_tokens_tokenable_type_tokenable_id_index (tokenable_type, tokenable_id)
);
```

### **Social Media Table**
```sql
CREATE TABLE social_media (
    social_media_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    platform VARCHAR(50) NOT NULL,         -- 'Facebook', 'Instagram', etc.
    url VARCHAR(255) NOT NULL UNIQUE       -- Social media URL
);
```

### **User Table**
```sql
CREATE TABLE user (
    student_number VARCHAR(20) PRIMARY KEY,
    password VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (student_number) REFERENCES student(student_number)
);
```

## 🔐 **Security & Access Control**

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                              ACCESS CONTROL FLOW                             │
└─────────────────────────────────────────────────────────────────────────────┘

USER REQUEST
    │
    ▼
TOKEN VALIDATION
    │
    ▼
PERMISSION CHECK
    │
    ▼
ROLE VERIFICATION
    │
    ▼
FEATURE ACCESS
    │
    ▼
RESPONSE
```

## 📊 **Summary**

| Component | Relationship Type | Purpose | Connection |
|-----------|------------------|---------|------------|
| **Personal Access Tokens** | Direct | API Authentication | Links to User via tokenable_id |
| **Migration** | Direct | Schema Modification | Changes tokenable_id data type |
| **Social Media** | Indirect | Business Feature | Accessible via authenticated API |
| **User** | Direct | Authentication | Primary entity for tokens |
| **Student** | Direct | User Extension | 1:1 with User table |

The **Personal Access Tokens** serve as the **authentication bridge** that enables users to access the **Social Media management features** (and all other API endpoints), while the **Migration** ensures the token system can properly reference different entity types in the polymorphic relationship system.
