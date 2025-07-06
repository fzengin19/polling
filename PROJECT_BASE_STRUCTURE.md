# Proje Veritabanı ve Model Yapısı

Bu döküman, projedeki tüm modellerin sütunlarını ve Eloquent ilişkilerini detaylandırmaktadır. Geliştirme sırasında tutarlılığı sağlamak için bir referans olarak kullanılmalıdır.

---

##  Behavioral Models (Spatie)

- **`Media`**: `spatie/laravel-medialibrary` tarafından yönetilir. Medya dosyaları hakkında bilgi tutar.
- **`Role`, `Permission`, `model_has_roles`, vb.**: `spatie/laravel-permission` tarafından yönetilir. Rol ve yetki bilgilerini tutar.

---

## Core Models

### 👤 User Modeli
- **Tablo:** `users`
- **Açıklama:** Uygulamadaki kullanıcıları temsil eder.

#### Sütunlar
- `id` (PK, BigInt, Unsigned)
- `name` (String)
- `email` (String, Unique)
- `email_verified_at` (Timestamp, Nullable)
- `password` (String)
- `provider` (String, Nullable)
- `provider_id` (String, Nullable)
- `remember_token` (String, Nullable)
- `created_at` (Timestamp, Nullable)
- `updated_at` (Timestamp, Nullable)

#### İlişkiler
- `surveys()`: `HasMany` -> `Survey` (Kullanıcının oluşturduğu anketler)
- `templates()`: `HasMany` -> `Template` (Kullanıcının oluşturduğu şablonlar)
- `responses()`: `HasMany` -> `Response` (Kullanıcının verdiği yanıtlar)

### 📋 Template Modeli
- **Tablo:** `templates`
- **Açıklama:** Yeniden kullanılabilir anket taslakları (şablonlar).

#### Sütunlar
- `id` (PK, BigInt, Unsigned)
- `title` (String)
- `description` (Text, Nullable)
- `is_public` (Boolean, Default: `false`)
- `created_by` (Foreign Key -> `users.id`)
- `forked_from_template_id` (Foreign Key -> `templates.id`, Nullable)
- `created_at` (Timestamp, Nullable)
- `updated_at` (Timestamp, Nullable)

#### İlişkiler
- `creator()`: `BelongsTo` -> `User` (Şablonu oluşturan kullanıcı)
- `forkedFrom()`: `BelongsTo` -> `Template` (Çatallandığı ana şablon)
- `forks()`: `HasMany` -> `Template` (Bu şablondan çatallananlar)
- `surveys()`: `HasMany` -> `Survey` (Bu şablonu kullanan anketler)
- `versions()`: `HasMany` -> `TemplateVersion` (Şablonun versiyonları)
- `latestVersion()`: `HasOne` -> `TemplateVersion` (En son versiyon)

### 🔄 TemplateVersion Modeli
- **Tablo:** `template_versions`
- **Açıklama:** Bir şablonun belirli bir zamandaki anlık görüntüsünü (snapshot) tutar.

#### Sütunlar
- `id` (PK, BigInt, Unsigned)
- `template_id` (Foreign Key -> `templates.id`, OnDelete: Cascade)
- `version` (String, Örn: "1.0.0")
- `snapshot` (JSON)
- `created_at` (Timestamp, Nullable)
- `updated_at` (Timestamp, Nullable)

#### İlişkiler
- `template()`: `BelongsTo` -> `Template` (Ait olduğu şablon)

### 📊 Survey Modeli
- **Tablo:** `surveys`
- **Açıklama:** Kullanıcıların yanıtlaması için oluşturulan anketler.

#### Sütunlar
- `id` (PK, BigInt, Unsigned)
- `title` (String)
- `description` (Text, Nullable)
- `status` (String, Enum: 'draft', 'active', 'archived', Default: 'draft')
- `created_by` (Foreign Key -> `users.id`)
- `template_id` (Foreign Key -> `templates.id`, Nullable)
- `template_version_id` (Foreign Key -> `template_versions.id`, Nullable)
- `settings` (JSON, Nullable)
- `expires_at` (Timestamp, Nullable)
- `max_responses` (Integer, Nullable)
- `created_at` (Timestamp, Nullable)
- `updated_at` (Timestamp, Nullable)

#### İlişkiler
- `creator()`: `BelongsTo` -> `User` (Anketi oluşturan kullanıcı)
- `template()`: `BelongsTo` -> `Template` (Anketin oluşturulduğu şablon)
- `templateVersion()`: `BelongsTo` -> `TemplateVersion`
- `pages()`: `HasMany` -> `SurveyPage` (Anketin sayfaları)
- `questions()`: `HasManyThrough` -> `Question` (Sayfalar üzerinden anketin soruları)
- `responses()`: `HasMany` -> `Response` (Ankete verilen yanıtlar)

### 📄 SurveyPage Modeli
- **Tablo:** `survey_pages`
- **Açıklama:** Bir anket içindeki sayfaları temsil eder.

#### Sütunlar
- `id` (PK, BigInt, Unsigned)
- `survey_id` (Foreign Key -> `surveys.id`, OnDelete: Cascade)
- `order_index` (Integer, Default: `0`)
- `title` (String, Nullable)
- `created_at` (Timestamp, Nullable)
- `updated_at` (Timestamp, Nullable)
- **Constraint:** `unique(['survey_id', 'order_index'])`

#### İlişkiler
- `survey()`: `BelongsTo` -> `Survey` (Ait olduğu anket)
- `questions()`: `HasMany` -> `Question` (Sayfadaki sorular)

### ❓ Question Modeli
- **Tablo:** `questions`
- **Açıklama:** Bir anket sayfasındaki sorular.

#### Sütunlar
- `id` (PK, BigInt, Unsigned)
- `page_id` (Foreign Key -> `survey_pages.id`, OnDelete: Cascade)
- `type` (String, Örn: 'text', 'multiple_choice')
- `title` (Text)
- `is_required` (Boolean, Default: `false`)
- `help_text` (Text, Nullable)
- `placeholder` (String, Nullable)
- `config` (JSON, Nullable)
- `order_index` (Integer, Default: `0`)
- `created_at` (Timestamp, Nullable)
- `updated_at` (Timestamp, Nullable)
- **Constraint:** `unique(['page_id', 'order_index'])`

#### İlişkiler
- `surveyPage()`: `BelongsTo` -> `SurveyPage` (Ait olduğu sayfa)
- `choices()`: `HasMany` -> `Choice` (Soruya ait seçenekler)
- `answers()`: `HasMany` -> `Answer` (Soruya verilen cevaplar)

### 🔘 Choice Modeli
- **Tablo:** `choices`
- **Açıklama:** Çoktan seçmeli gibi soruların seçenekleri.

#### Sütunlar
- `id` (PK, BigInt, Unsigned)
- `question_id` (Foreign Key -> `questions.id`, OnDelete: Cascade)
- `label` (String)
- `value` (String)
- `order_index` (Integer, Default: `0`)
- `created_at` (Timestamp, Nullable)
- `updated_at` (Timestamp, Nullable)
- **Constraint:** `unique(['question_id', 'order_index'])`

#### İlişkiler
- `question()`: `BelongsTo` -> `Question` (Ait olduğu soru)

### 📤 Response Modeli
- **Tablo:** `responses`
- **Açıklama:** Bir kullanıcının bir ankete verdiği yanıtların genel kaydı.

#### Sütunlar
- `id` (PK, BigInt, Unsigned)
- `survey_id` (Foreign Key -> `surveys.id`, OnDelete: Cascade)
- `user_id` (Foreign Key -> `users.id`, Nullable)
- `is_complete` (Boolean, Default: `false`)
- `submitted_at` (Timestamp, Nullable)
- `metadata` (JSON, Nullable)
- `created_at` (Timestamp, Nullable)
- `updated_at` (Timestamp, Nullable)

#### İlişkiler
- `survey()`: `BelongsTo` -> `Survey`
- `user()`: `BelongsTo` -> `User`
- `answers()`: `HasMany` -> `Answer`

### 📝 Answer Modeli
- **Tablo:** `answers`
- **Açıklama:** Bir yanıta ait tek bir soruya verilen cevap.

#### Sütunlar
- `id` (PK, BigInt, Unsigned)
- `response_id` (Foreign Key -> `responses.id`, OnDelete: Cascade)
- `question_id` (Foreign Key -> `questions.id`, OnDelete: Cascade)
- `choice_id` (Foreign Key -> `choices.id`, Nullable, OnDelete: Set Null)
- `value` (Text, Nullable)
- `created_at` (Timestamp, Nullable)
- `updated_at` (Timestamp, Nullable)

#### İlişkiler
- `response()`: `BelongsTo` -> `Response`
- `question()`: `BelongsTo` -> `Question`
- `choice()`: `BelongsTo` -> `Choice` 