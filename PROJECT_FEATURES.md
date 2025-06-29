# Proje Özellikleri ve API Dökümantasyonu

Bu döküman, projenin testleri analiz edilerek otomatik olarak oluşturulmuştur ve mevcut API'nin özelliklerini, endpoint'lerini ve iş mantığını özetlemektedir.

## 1. Kimlik Doğrulama (Authentication)

- **Endpoint:** `POST /api/auth/register`
  - **Açıklama:** Yeni kullanıcı kaydı oluşturur.
  - **Giriş:** `name`, `email`, `password`, `password_confirmation`.
  - **Başarı Yanıtı:** `201 Created` durum kodu ile kullanıcı bilgileri ve bir adet API token'ı döner.
- **Endpoint:** `POST /api/auth/login`
  - **Açıklama:** Kullanıcı girişi yaparak API token'ı alır.
  - **Giriş:** `email`, `password`.
  - **Başarı Yanıtı:** `200 OK` ile kullanıcı bilgileri ve API token'ı döner.
- **Endpoint:** `POST /api/auth/logout` (Doğrulama Gerekli)
  - **Açıklama:** Kullanıcının mevcut token'ını geçersiz kılarak çıkış yapar.
- **Endpoint:** `GET /api/auth/me` (Doğrulama Gerekli)
  - **Açıklama:** Mevcut giriş yapmış kullanıcının bilgilerini döner.
- **Endpoint:** `POST /api/auth/google`
  - **Açıklama:** Google Access Token ile kullanıcı girişi veya kaydı yapar.

## 2. Anketler (Surveys)

- **CRUD İşlemleri:**
  - `GET /api/surveys`: Tüm anketleri listeler.
  - `POST /api/surveys`: Yeni bir anket oluşturur. (Doğrulama Gerekli)
  - `GET /api/surveys/{id}`: Belirli bir anketi gösterir.
  - `PUT /api/surveys/{id}`: Belirli bir anketi günceller. (Doğrulama Gerekli)
  - `DELETE /api/surveys/{id}`: Belirli bir anketi siler. (Doğrulama Gerekli)
- **Ek Özellikler:**
  - `GET /api/surveys/active`: Sadece aktif olan anketleri listeler.
  - `GET /api/surveys/my`: Giriş yapmış kullanıcının anketlerini listeler. (Doğrulama Gerekli)
  - `POST /api/surveys/{id}/publish`: Bir anketi yayınlar. (Doğrulama Gerekli)
  - `POST /api/surveys/{id}/archive`: Bir anketi arşivler. (Doğrulama Gerekli)
  - `POST /api/surveys/{id}/duplicate`: Bir anketin kopyasını oluşturur. (Doğrulama Gerekli)
- **Anket Sayfaları (Survey Pages):**
  - `GET /api/surveys/{surveyId}/pages`: Bir ankete ait tüm sayfaları listeler.
  - `POST /api/pages`: Yeni bir anket sayfası oluşturur. (Doğrulama Gerekli)
  - `PUT /api/pages/{id}`: Bir anket sayfasını günceller. (Doğrulama Gerekli)
  - `DELETE /api/pages/{id}`: Bir anket sayfasını siler. (Doğrulama Gerekli)
  - `POST /api/surveys/{surveyId}/pages/reorder`: Bir ankete ait sayfaları yeniden sıralar. (Doğrulama Gerekli)

## 3. Sorular ve Seçenekler (Questions & Choices)

- **Soru İşlemleri:** (Tümü Doğrulama Gerekli)
  - `GET /api/survey-pages/{pageId}/questions`: Bir anket sayfasına ait tüm soruları listeler.
  - `POST /api/survey-pages/{pageId}/questions`: Bir sayfaya yeni soru ekler.
  - `GET /api/questions/{id}`: Belirli bir soruyu gösterir.
  - `PUT /api/questions/{id}`: Bir soruyu günceller.
  - `DELETE /api/questions/{id}`: Bir soruyu siler.
  - `POST /api/questions/reorder`: Bir sayfadaki soruları yeniden sıralar.
- **Seçenek İşlemleri:** (Tümü Doğrulama Gerekli)
  - `GET /api/questions/{questionId}/choices`: Bir soruya ait tüm seçenekleri listeler.
  - `POST /api/questions/{questionId}/choices`: Bir soruya yeni seçenek ekler. Yeni eklenen seçenek, `order_index`'i otomatik olarak artırılarak sona eklenir. Sadece `multiple_choice` türündeki sorulara eklenebilir.
  - `PUT /api/choices/{id}`: Belirli bir seçeneği günceller.
  - `DELETE /api/choices/{id}`: Belirli bir seçeneği siler.
  - `POST /api/questions/{questionId}/choices/reorder`: Bir soruya ait seçenekleri yeniden sıralar.

## 4. Medya Yönetimi (Media Management)

- **Genel Medya Yükleme:** (Doğrulama Gerekli)
  - **Endpoint:** `POST /api/enhanced-media/{modelType}/{modelId}/upload`
  - **Açıklama:** Belirtilen modele (`survey`, `survey-page`, `question`, `choice`) medya dosyası yükler.
  - **Giriş:** `file` (dosya), `collection` (isteğe bağlı).
  - **Validasyon:** Sadece `file` alanı zorunludur. Modele özgü (`question_id` gibi) bir alan zorunluluğu yoktur.
- **Mevcut Medyayı Yönetme:** (Doğrulama Gerekli)
  - `GET /api/enhanced-media/{modelType}/{modelId}/media`: Bir modele ait medyaları listeler.
  - `PUT /api/media/{mediaId}/metadata`: Bir medya dosyasının meta verilerini (örn: alt text, caption) günceller.
  - `DELETE /api/media/{mediaId}`: Bir medya dosyasını siler.

## 5. Yanıtlar ve İstatistikler (Responses & Statistics)

- **Yanıt Verme Akışı:**
  - `POST /api/responses`: Bir ankete yanıt vermeye başlar ve bir yanıt kaydı oluşturur.
  - `POST /api/responses/{id}/submit`: Oluşturulan yanıtı, cevapları ile birlikte göndererek tamamlar.
- **İstatistikler:**
  - `GET /api/surveys/{id}/responses`: Bir ankete ait yanıt istatistiklerini getirir.

## 6. Roller ve Yetkilendirme (Roles & Authorization)

- **Rol Atama/Kaldırma:** (Tümü Doğrulama Gerekli)
  - `POST /api/roles/assign`: Bir kullanıcıya veya ankete rol atar.
  - `POST /api/roles/remove`: Bir kullanıcıdan veya anketten rolü kaldırır.
- **Kontrol:** (Tümü Doğrulama Gerekli)
  - `GET /api/roles`: Tüm mevcut rolleri listeler.
  - `GET /api/roles/users/{userId}`: Bir kullanıcının rollerini listeler.
  - `GET /api/roles/surveys/{surveyId}`: Bir anketin rollerini listeler.

## 7. Şablonlar (Templates)

- **CRUD İşlemleri:**
  - `GET /api/templates`: Herkese açık tüm şablonları listeler.
  - `POST /api/templates`: Yeni bir şablon oluşturur. (Doğrulama Gerekli)
  - `GET /api/templates/{id}`: Belirli bir şablonu gösterir.
  - `PUT /api/templates/{id}`: Bir şablonu günceller. (Doğrulama Gerekli)
- **Ek Özellikler:**
  - `GET /api/templates/my`: Giriş yapmış kullanıcının şablonlarını listeler. (Doğrulama Gerekli)
  - `POST /api/templates/{id}/fork`: Bir şablonu kopyalayarak yeni bir şablon oluşturur. (Doğrulama Gerekli)
  - **Sürüm Yönetimi:** Versiyon oluşturma ve geri yükleme özellikleri desteklenmektedir. 