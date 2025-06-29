# Backend Odaklı Roadmap

## Model ve Sütun Tanımları

### 🔑 User Modeli
- [x] id (PK)
- [x] name
- [x] email (unique)
- [x] password
- [x] provider (nullable) - Google, Facebook vb.
- [x] provider_id (nullable)
- [x] created_at
- [x] updated_at

### 📋 Template Modeli
- [x] id (PK)
- [x] title
- [x] description (nullable)
- [x] is_public (boolean, default: false)
- [x] created_by (FK: users.id)
- [x] forked_from_template_id (nullable, FK: templates.id)
- [x] created_at
- [x] updated_at

### 🔄 TemplateVersion Modeli
- [x] id (PK)
- [x] template_id (FK: templates.id)
- [x] version (string, semantic versioning)
- [x] snapshot (JSON) - Template'in o anki tam durumu
- [x] created_at

### 📊 Survey Modeli
- [x] id (PK)
- [x] title
- [x] description (nullable)
- [x] status (enum: draft, active, archived)
- [x] created_by (FK: users.id)
- [x] template_id (nullable, FK: templates.id)
- [x] template_version_id (nullable, FK: template_versions.id)
- [x] settings (JSON, nullable) - Anket ayarları (anonim, çoklu yanıt, vb.)
- [x] expires_at (timestamp, nullable) - Bitiş tarihi
- [x] max_responses (integer, nullable) - Maksimum yanıt sayısı
- [x] created_at
- [x] updated_at

### 📄 SurveyPage Modeli
- [x] id (PK)
- [x] survey_id (FK: surveys.id)
- [x] order_index (integer, default: 0)
- [x] title (nullable)
- [x] created_at
- [x] updated_at

### ❓ Question Modeli
- [x] id (PK)
- [x] page_id (FK: survey_pages.id)
- [x] type (string: text, multiple_choice, rating, etc.)
- [x] title
- [x] is_required (boolean, default: false)
- [x] help_text (text, nullable) - Yardım metni
- [x] placeholder (text, nullable) - Placeholder metni
- [x] config (JSON, nullable) - Validasyon, koşullu mantık, medya referansları
- [x] order_index (integer, default: 0)
- [x] created_at
- [x] updated_at

### 🔘 Choice Modeli
- [x] id (PK)
- [x] question_id (FK: questions.id)
- [x] label
- [x] value
- [x] order_index (integer, default: 0)
- [x] created_at
- [x] updated_at

### 📤 Response Modeli
- [x] id (PK)
- [x] survey_id (FK: surveys.id)
- [x] user_id (nullable, FK: users.id) - Anonim yanıtlar için
- [x] started_at (timestamp, nullable) - Başlangıç zamanı
- [x] submitted_at (timestamp, nullable) - Tamamlanma zamanı
- [x] metadata (JSON, nullable) - IP, user_agent, vb.
- [x] is_complete (boolean, default: false)
- [x] created_at
- [x] updated_at

### 📝 Answer Modeli
- [x] id (PK)
- [x] response_id (FK: responses.id)
- [x] question_id (FK: questions.id)
- [x] choice_id (nullable, FK: choices.id)
- [x] value (text, nullable)
- [x] order_index (integer, default: 0) - Çoklu cevap sıralaması
- [x] created_at

### 🎭 Role/Permission Tabloları (spatie/laravel-permission)
- [x] roles
- [x] permissions
- [x] model_has_roles
- [x] model_has_permissions
- [x] role_has_permissions

### 📸 Media Tabloları (spatie/laravel-medialibrary)
- [x] media
- [x] model_has_media

### 📜 Activity Log Tabloları (spatie/laravel-activitylog)
- [ ] activity_log

---

## Geliştirme Adımları

- [x] **Adım 1: Çekirdek Yapı ve Temel API'ler** ✅ TAMAMLANDI
  - [x] Migrations: users, templates, surveys, survey_pages, questions
  - [x] Modeller ve ilişkilerin tanımlanması
  - [x] Temel CRUD API'leri:
    - [x] Survey oluşturma/güncelleme/listeleme
    - [x] SurveyPage ekleme/silme/sıralama
    - [x] Question ekleme/güncelleme/silme
    - [x] Choice ekleme/güncelleme/silme
    - [x] Response başlatma
    - [x] Answer kaydetme
    - [x] Response tamamlama

- [x] **Adım 2: Rol ve Yetki Yönetimi** ✅ TAMAMLANDI
  - [x] spatie/laravel-permission kurulumu
  - [x] Survey modeline HasRoles trait eklenmesi
  - [x] Rol atama/kaldırma API'leri
  - [x] Middleware ile erişim kontrolü

- [x] **Adım 3: Medya Yönetimi** ✅ TAMAMLANDI
  - [x] spatie/laravel-medialibrary kurulumu
  - [x] Question modeline medya desteği eklenmesi
  - [x] Medya yükleme/silme API endpointleri

- [x] **Adım 3.1: Medya Sistemi Geliştirmeleri** ✅ TAMAMLANDI
  - [x] Choice modeline HasMedia trait eklenmesi
  - [x] Survey modeline medya desteği eklenmesi
  - [x] SurveyPage modeline medya desteği eklenmesi
  - [x] Media collection'ları spesifikleştirme (question-images, choice-images, survey-banners, etc.)
  - [ ] Question.config'de medya referanslarını standardize etme
  - [x] Gelişmiş medya API endpointleri ve testleri

- [ ] **Adım 4: Audit Log Sistemi**
  - [ ] spatie/laravel-activitylog kurulumu
  - [ ] Loglanacak olayların tanımlanması
  - [ ] GET /api/surveys/{id}/activity endpointi

- [ ] **Adım 5: Güvenlik ve Optimizasyon**
  - [ ] Rate Limiting (Laravel native)
  - [ ] survey_id + IP kombinasyonu ile sınırlandırma
  - [ ] DB indeksleme (survey_id, question_id, response_id)
  - [ ] Task Scheduling: Günlük arşivleme, eski draft temizliği

- [x] **Adım 6: Şablon Yönetimi** ✅ TAMAMLANDI
  - [x] Template CRUD API'leri
  - [x] Şablon seçimi ve detay endpointleri

- [x] **Adım 7: Şablon Versiyonlama** ✅ TAMAMLANDI
  - [x] template_versions tablosu
  - [x] Versiyon listeleme ve geri alma API'leri

- [x] **Adım 8: Raporlama ve Analitik**
  - [x] Temel istatistik endpointleri (Survey, Template, Question sayıları) ✅ TAMAMLANDI
  - [ ] Cevap dağılımları için detaylı istatistikler
  - [ ] Sonuçları dışa aktarma (CSV/Excel/PDF)

- [ ] **Adım 9: Koşullu Mantık (Conditional Logic)** (ÖNCELİKLİ)
  - [ ] Question.config yapısını, "Eğer Cevap X ise, Soru Y'ye atla" gibi kuralları destekleyecek şekilde genişletme.
  - [ ] Koşul tiplerinin tanımlanması (örn: 'equals', 'contains', 'is_selected').
  - [ ] Frontend'in bu mantığı işleyebilmesi için API yanıtında gerekli yönlendirme bilgisini sağlama.
  - [ ] Koşullu mantık kurallarını yönetmek için yeni API endpoint'leri (örn: `POST /api/questions/{id}/logic`).

- [ ] **Adım 10: Gelişmiş Soru Tipleri**
  - [ ] Checkbox (Çoklu Onay Kutusu)
  - [ ] Dropdown (Açılır Menü)
  - [ ] Linear Scale (Doğrusal Ölçek, örn: 1-5 arası puanlama)
  - [ ] Date / Time (Tarih / Saat)
  - [ ] File Upload (Yanıt olarak dosya yükleme)
  - [ ] İlgili validasyon kurallarının (`Question.config`) ve `Answer.value` yapısının bu tipleri desteklemesi.

- [ ] **Adım 11: Gelişmiş Anket Ayarları ve Özelleştirme**
  - [ ] `Survey.settings` JSON sütununu kullanarak yeni ayarları yönetme.
  - [ ] Erişim Kontrolü: "Sadece belirli e-posta adresleri yanıtlayabilir" veya "Sadece bir yanıt kabul et" gibi kurallar.
  - [ ] Görsel Özelleştirme: Tema rengi, arka plan resmi gibi ayarları saklamak için API altyapısı.
  - [ ] Zamanlanmış anket yayınlama ve kapatma.

- [ ] **Adım 12: Gelişmiş İş Birliği Özellikleri**
  - [ ] Mevcut rol sistemini genişletme: `owner`, `editor`, `viewer` gibi daha granüler roller tanımlama.
  - [ ] Bir anketi başka kullanıcılarla paylaşmak için API endpoint'leri (örn: `POST /api/surveys/{id}/collaborators`).

- [ ] **Adım 13: Arama ve Keşfetme**
  - [ ] Laravel Scout + Meilisearch/Algolia entegrasyonu ile anketler ve şablonlar içinde hızlı arama.

- [ ] **Adım 14: Çoklu Dil Desteği**
  - [ ] Anket başlığı, soruları ve seçenekleri için JSON tabanlı dil çevirilerini destekleme.

---

## Test ve Dağıtım Planı

- [x] Birim Testler (Model ilişkileri, DB constraint'leri) ✅ TAMAMLANDI
- [x] Feature Testler (CRUD, rol bazlı erişim, response akışı) ✅ TAMAMLANDI
- [ ] Stres Testleri (Çoklu kullanıcı, büyük veri)
- [ ] CI/CD pipeline kurulumu

---

## In-Memory Cache ve Batch Senkronizasyon Notları

- [ ] Response/Answer işlemlerini servis katmanında soyutla, ileride cache'e geçiş kolay olsun.
- [ ] Task Scheduling ile batch DB yazımı için altyapı hazırla.
- [ ] DB transaction ve veri tutarlılığı için idempotent endpointler ve event tabanlı mimariyi değerlendir.
- [ ] Testlerde cache ve DB senkronizasyonunu ayrı ayrı simüle et.

> **Not:** Planın mevcut hali, ileride Dragonfly gibi bir in-memory cache ve batch senkronizasyon mimarisi için uygundur. Servis katmanında soyutlama ve task scheduling adımlarına dikkat et.





bak sen gerizekalı mısın max min gibi kontroller de veri formatına dahil sayılır bunu akıl edemiyor musun? migration dosyalarına ve @roadmap.md  dosyasına bak ve bunlarla tutarlı şekilde tüm requestlerii kontrol et rulesleri ve bodyParameters methodunu düzgünce güncelle. request sınıfları min max regex type gibi şeyleri kontrol etcek ama exists gibi iş mantığı saılacak şeyler servis katmanında olcak bunu akıl etmek tutarlı korumak çok mu zor neyini anlamıyorsun anlamadıysan sor bana valla çok basit lan.