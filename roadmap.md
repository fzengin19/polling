# 🚀 Development Roadmap for Anonymous Polling App

Her aşamayı tamamladikça ✅ işaretle, detayları o aşamayı geliştirirken tek tek ele alacağız.

---

## 1. Proje Hazırlığı  
- [x] Laravel projesini oluştur ve PostgreSQL + Redis bağlantılarını ayarla  
- [x] Geliştirme ortamını (Docker veya yerel) hazırla  

## 2. Temel Veri Modeli  
- [x] `Poll`, `Option` ve `PollVote` tablolarının migration’larını tanımla  
- [x] UUID ve temel alanlar (title, starts_at, ends_at vb.) ekle  

## 3. API & Controller Yapısı  
- [ ] Poll yönetimi (oluşturma, listeme) endpoint’lerini planla  
- [ ] Oy verme endpoint’ini planla (Redis entegrasyonu)  
- [ ] Sonuç sorgulama endpoint’ini planla  

## 4. Redis ile Oylama Mekanizması  
- [ ] Redis key formatını ve sayaç stratejisini belirle  
- [ ] Arka plan senkronizasyon iş akışını (job/scheduler) tasarla  

## 5. Frontend & UI Tasarımı  
- [ ] Anket oluşturma formu ve listeleme sayfası mockup’larını çiz  
- [ ] Oy verme ekranı ve canlı sonuç panosunun wireframe’ini yap  

## 6. Google OAuth Entegrasyonu  
- [ ] Google ile isteğe bağlı giriş akışını tasarla  
- [ ] Anonim mod ve kayıtlı kullanıcı modunu ayırt edecek kontrolleri planla  

## 7. Test & QA Stratejisi  
- [ ] Birim ve entegrasyon testlerinin kapsamını belirle  
- [ ] Yük testleri için senaryoları oluştur  

## 8. Deployment & Monitoring  
- [ ] CI/CD pipeline tasarımı (build, test, deploy adımları)  
- [ ] Loglama, alert ve temel monitoring kurulum planı  

---

Her başlığı geliştirmeye başladığında, o adıma özgü ayrıntılı görev listesini birlikte çıkarabiliriz. Hangi adımdan başlamak istersin? 🚩  
