# Backend Odaklı Roadmap

## Model ve Sütun Tanımları

### 🔑 User Modeli
- id (PK)
- name
- email (unique)
- password
- provider (nullable) - Google, Facebook vb.
- provider_id (nullable)
- created_at
- updated_at

### 📋 Template Modeli
- id (PK)
- title
- description (nullable)
- is_public (boolean, default: false)
- created_by (FK: users.id)
- forked_from_template_id (nullable, FK: templates.id)
- created_at
- updated_at

### 🔄 TemplateVersion Modeli
- id (PK)
- template_id (FK: templates.id)
- version (string, semantic versioning)
- snapshot (JSON) - Template'in o anki tam durumu
- created_at

### 📊 Survey Modeli
- id (PK)
- title
- description (nullable)
- status (enum: draft, active, archived)
- created_by (FK: users.id)
- template_id (nullable, FK: templates.id)
- template_version_id (nullable, FK: template_versions.id)
- settings (JSON, nullable) - Anket ayarları (anonim, çoklu yanıt, vb.)
- expires_at (timestamp, nullable) - Bitiş tarihi
- max_responses (integer, nullable) - Maksimum yanıt sayısı
- created_at
- updated_at

### 📄 SurveyPage Modeli
- id (PK)
- survey_id (FK: surveys.id)
- order_index (integer, default: 0)
- title (nullable)
- created_at
- updated_at

### ❓ Question Modeli
- id (PK)
- page_id (FK: survey_pages.id)
- type (string: text, multiple_choice, rating, etc.)
- title
- is_required (boolean, default: false)
- help_text (text, nullable) - Yardım metni
- placeholder (text, nullable) - Placeholder metni
- config (JSON, nullable) - Validasyon, koşullu mantık, medya referansları
- order_index (integer, default: 0)
- created_at
- updated_at

### 🔘 Choice Modeli
- id (PK)
- question_id (FK: questions.id)
- label
- value
- order_index (integer, default: 0)
- created_at
- updated_at

### 📤 Response Modeli
- id (PK)
- survey_id (FK: surveys.id)
- user_id (nullable, FK: users.id) - Anonim yanıtlar için
- started_at (timestamp, nullable) - Başlangıç zamanı
- submitted_at (timestamp, nullable) - Tamamlanma zamanı
- metadata (JSON, nullable) - IP, user_agent, vb.
- is_complete (boolean, default: false)
- created_at
- updated_at

### 📝 Answer Modeli
- id (PK)
- response_id (FK: responses.id)
- question_id (FK: questions.id)
- choice_id (nullable, FK: choices.id)
- value (text, nullable)
- order_index (integer, default: 0) - Çoklu cevap sıralaması
- created_at

### 🎭 Role/Permission Tabloları (spatie/laravel-permission)
- roles
- permissions
- model_has_roles
- model_has_permissions
- role_has_permissions

### 📸 Media Tabloları (spatie/laravel-medialibrary)
- media
- model_has_media

### 📜 Activity Log Tabloları (spatie/laravel-activitylog)
- activity_log

---

## Geliştirme Adımları

- [ ] **Adım 1: Çekirdek Yapı ve Temel API'ler**
  - [ ] Migrations: users, templates, surveys, survey_pages, questions, choices, responses, answers
  - [ ] Modeller ve ilişkilerin tanımlanması
  - [ ] Temel CRUD API'leri:
    - [ ] Survey oluşturma/güncelleme/listeleme
    - [ ] SurveyPage ekleme/silme/sıralama
    - [ ] Question ekleme/güncelleme/silme
    - [ ] Choice ekleme/güncelleme/silme
    - [ ] Response başlatma
    - [ ] Answer kaydetme
    - [ ] Response tamamlama

- [ ] **Adım 2: Rol ve Yetki Yönetimi**
  - [ ] spatie/laravel-permission kurulumu
  - [ ] Survey modeline HasRoles trait eklenmesi
  - [ ] Rol atama/kaldırma API'leri
  - [ ] Middleware ile erişim kontrolü

- [ ] **Adım 3: Medya Yönetimi**
  - [ ] spatie/laravel-medialibrary kurulumu
  - [ ] Question modeline medya desteği eklenmesi
  - [ ] Medya yükleme/silme API endpointleri

- [ ] **Adım 4: Audit Log Sistemi**
  - [ ] spatie/laravel-activitylog kurulumu
  - [ ] Loglanacak olayların tanımlanması
  - [ ] GET /api/surveys/{id}/activity endpointi

- [ ] **Adım 5: Güvenlik ve Optimizasyon**
  - [ ] Rate Limiting (Laravel native)
  - [ ] survey_id + IP kombinasyonu ile sınırlandırma
  - [ ] DB indeksleme (survey_id, question_id, response_id)
  - [ ] Task Scheduling: Günlük arşivleme, eski draft temizliği

- [ ] **Adım 6: Şablon Yönetimi**
  - [ ] Template CRUD API'leri
  - [ ] Şablon seçimi ve detay endpointleri

- [ ] **Adım 7: Şablon Versiyonlama**
  - [ ] template_versions tablosu
  - [ ] Versiyon listeleme ve geri alma API'leri

- [ ] **Adım 8: Raporlama ve Analitik**
  - [ ] Temel istatistik endpointleri
  - [ ] Cevap dağılımları
  - [ ] Dışa aktarma (CSV/Excel/PDF)

- [ ] **Adım 9: Koşullu Mantık ve Gelişmiş Validasyon**
  - [ ] Question.config ile koşul ekleme
  - [ ] Backend validasyon motoru
  - [ ] Kısmi kayıt desteği

- [ ] **Adım 10: Gelişmiş Özellikler**
  - [ ] Laravel Scout + Meilisearch entegrasyonu
  - [ ] Zamanlanmış anket yayınlama
  - [ ] Webhook entegrasyonları
  - [ ] Çoklu dil desteği

---

## Test ve Dağıtım Planı

- [ ] Birim Testler (Model ilişkileri, DB constraint'leri)
- [ ] Feature Testler (CRUD, rol bazlı erişim, response akışı)
- [ ] Stres Testleri (Çoklu kullanıcı, büyük veri)
- [ ] CI/CD pipeline kurulumu

---

## In-Memory Cache ve Batch Senkronizasyon Notları

- [ ] Response/Answer işlemlerini servis katmanında soyutla, ileride cache'e geçiş kolay olsun.
- [ ] Task Scheduling ile batch DB yazımı için altyapı hazırla.
- [ ] DB transaction ve veri tutarlılığı için idempotent endpointler ve event tabanlı mimariyi değerlendir.
- [ ] Testlerde cache ve DB senkronizasyonunu ayrı ayrı simüle et.

> **Not:** Planın mevcut hali, ileride Dragonfly gibi bir in-memory cache ve batch senkronizasyon mimarisi için uygundur. Servis katmanında soyutlama ve task scheduling adımlarına dikkat et.

