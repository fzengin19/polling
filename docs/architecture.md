# Laravel Polling Projesi Mimari Analiz ve Geliştirme Rehberi

## 1. Genel Mimari ve Katmanlar

Bu proje, modern Laravel mimarisiyle, katmanlı ve SOLID prensiplerine uygun olarak geliştirilmiştir. Temel katmanlar şunlardır:

- **Controller**: HTTP isteklerini karşılar, validasyon ve DTO dönüşümü yapar, servis katmanına yönlendirir.
- **Request**: İstek validasyonunu ve authorize kontrolünü sağlar.
- **DTO (Data Transfer Object)**: Controller ile Service arasında veri taşır, tip güvenliği sağlar.
- **Service**: İş mantığı burada bulunur. Repository ve diğer servislerle konuşur.
- **Repository**: Veritabanı işlemlerini soyutlar, Eloquent ile veri erişimini yönetir.
- **Model**: Eloquent ORM ile veritabanı tablolarını temsil eder.
- **Resource**: API yanıtlarını standartlaştırır ve dönüştürür.
- **Response**: Servis katmanından dönen veriyi sarmalar ve uygun HTTP yanıtına çevirir.
- **Provider**: Bağımlılıkları (interface-implementation) Laravel IoC container'a bağlar.

## 2. Bir API İsteğinin Akışı (Örnek: Register/Login)

Aşağıda, bir kullanıcının `POST /api/auth/register` veya `POST /api/auth/login` isteğiyle başlayan ve veritabanına kadar giden akış detaylıca anlatılmıştır:

### 2.1. Route
- `routes/api.php` dosyasında, örneğin `Route::post('register', [AuthController::class, 'register']);` tanımı bulunur.

### 2.2. Controller
- `App\Http\Controllers\Api\AuthController`
- Gelen istek, ilgili FormRequest (`RegisterRequest` veya `LoginRequest`) ile validasyondan geçer(Burada form request sınıfları sadece ve sadece validasyon için kullanılır iş mantığına dahil olan kontroller servis katmanında ele alınmalıdır.).
- Validasyon başarılıysa, veriler DTO'ya (`RegisterDto` veya `LoginDto`) dönüştürülür.
- Controller, ilgili Service metodunu çağırır: `$this->authService->register($dto)`

### 2.3. Service
- `App\Services\Concrete\AuthService`
- İş mantığı burada bulunur. Örneğin, register akışında:
  - E-posta benzersizliği kontrol edilir.
  - Kullanıcı oluşturulur (şifre hashlenir).
  - Kullanıcıya API token üretilir (Sanctum).
  - Sonuç, ServiceResponse ile dönülür.
- Service, repository ve resource map gibi bağımlılıklarını provider üzerinden alır.

### 2.4. Repository
- `App\Repositories\Eloquent\UserRepository`
- Veritabanı işlemleri burada soyutlanır. Örneğin, `findByEmail` veya `create` metotları.
- Eloquent Model (`App\Models\User`) ile iletişim kurar.

### 2.5. Model
- `App\Models\User`
- Eloquent ORM ile `users` tablosunu temsil eder.
- `HasApiTokens` trait'i ile Sanctum token desteği sağlar.

### 2.6. Response & Resource
- Service katmanından dönen veri, `ServiceResponse` ile sarmalanır.
- `ServiceResponse`, ilgili Resource (ör: `UserResource`) ile veriyi API formatına dönüştürür.
- Sonuç, JSON olarak döner.

### 2.7. Provider
- `RepositoryServiceProvider`, `DomainServiceProvider`, `ResourceServiceProvider` gibi provider'lar, interface-implementation eşleşmelerini Laravel IoC container'a kaydeder.
- Böylece Controller ve Service katmanları, bağımlılıklarını otomatik olarak alır.

### 2.8. Middleware & Auth
- Giriş yapmış kullanıcıya özel endpointler için `auth:sanctum` middleware kullanılır.
- Token tabanlı kimlik doğrulama için Laravel Sanctum kullanılır.

## 3. Kullanılan Ana Yapılar ve Mekanizmalar

- **FormRequest**: Validasyon ve authorize işlemleri için kullanılır.
- **DTO**: Controller'dan Service'e tip güvenli veri aktarımı sağlar.
- **Service**: İş mantığı burada toplanır, controller sade kalır.
- **Repository**: Veritabanı işlemleri soyutlanır, test edilebilirlik artar.
- **Resource**: API yanıtları için standart ve genişletilebilir dönüşüm sağlar.
- **ServiceResponse**: Servis katmanından dönen veriyi sarmalar, resource ile dönüştürür.
- **Provider**: Bağımlılık enjeksiyonu için interface-implementation eşleşmeleri.
- **Sanctum**: Token tabanlı kimlik doğrulama.

## 4. Geliştirme Yaklaşımı ve Dikkat Edilecekler

- **SOLID ve Clean Code**: Her katman tek bir sorumluluğa sahip olmalı. Controller'da iş mantığı olmamalı.
- **Interface Kullanımı**: Servis ve repository'ler interface üzerinden kullanılmalı, böylece test edilebilirlik ve esneklik sağlanır.
- **DTO ile Tip Güvenliği**: Controller'dan Service'e veri aktarırken DTO kullanılmalı.
- **Validasyon**: Tüm girişler FormRequest ile validasyondan geçmeli.
- **Resource ile API Standartı**: API yanıtları resource ile dönüştürülmeli, doğrudan model dönülmemeli.
- **ServiceResponse ile Tutarlılık**: Tüm servis yanıtları ServiceResponse ile sarmalanmalı.
- **Provider ile Bağımlılık Yönetimi**: Yeni bir servis/repository eklerken ilgili provider'a binding eklenmeli.
- **Test Edilebilirlik**: Katmanlı yapı sayesinde birimler kolayca test edilebilir.
- **Genişletilebilirlik**: Yeni bir iş kuralı/özellik eklerken mevcut katmanlara uygun şekilde ekleme yapılmalı.
- **API Güvenliği**: Kimlik doğrulama ve yetkilendirme için middleware ve token tabanlı yapı kullanılmalı.
- **Merkezi Hata Yönetimi**: `bootstrap/app.php` içinde tanımlanan merkezi `Exceptions` yönetimi sayesinde, `AuthorizationException`, `ModelNotFoundException`, `ValidationException` gibi tüm yaygın uygulama hataları, API genelinde standart ve tutarlı bir JSON formatında (`{success, message, errors}`) otomatik olarak istemciye döndürülür. Bu, servis katmanında gereksiz `try-catch` bloklarını ve manuel hata yanıtı oluşturmayı engeller ve ServiceResponse yanıt formatı ile tutarlılığı sağlar.

---

## 5. Akış Diyagramı (Özet)

```mermaid
graph TD;
  A[API İsteği] --> B[Route]
  B --> C[Controller]
  C --> D[FormRequest (Validasyon)]
  D --> E[DTO]
  E --> F[Service]
  F --> G[Repository]
  G --> H[Model (Eloquent)]
  F --> I[ServiceResponse]
  I --> J[Resource]
  J --> K[JSON Response]
```

---

## 6. Ek Notlar
- Yeni bir özellik eklerken önce interface ve DTO'ları tanımlayın.
- Servis ve repository'leri test edilebilir şekilde yazın.
- API yanıtlarını resource ile dönüştürmeyi unutmayın.
- Provider'lara binding eklemeyi unutmayın.
- Kodunuzu küçük, okunabilir ve tek sorumluluk prensibine uygun tutun. 

---

## 7. API Yanıt Standardı

Tüm API yanıtları, frontend geliştirme sürecini kolaylaştırmak ve öngörülebilirliği artırmak için aşağıdaki standart JSend benzeri yapıyı takip edecektir:

```json
{
    "success": true,
    "data": { "id": 1, "title": "Anket Başlığı" },
    "message": "İşlem başarılı."
}
```

- **`success` (boolean):** İşlemin başarılı olup olmadığını belirtir (`true` veya `false`).
- **`data` (object|array|null):** Başarılı durumda asıl veri burada yer alır. Bu bir nesne, bir dizi nesne veya `null` olabilir. Başarısız durumda bu alan `null` olur.
- **`message` (string):** Hem başarı (`İşlem başarılı.`) hem de hata (`Kayıt bulunamadı.`) durumları için kullanıcıya gösterilebilecek, insan tarafından okunabilir bir metin içerir. 