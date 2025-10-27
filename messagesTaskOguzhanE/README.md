# 🚀 Mesaj Gönderme Servisi (Asenkron Kuyruk Mimarisi)

Bu proje, gelen API isteklerini asenkron (kuyruk tabanlı) bir yapıyla işleyerek, mesajları hız sınırlamasına uygun bir şekilde harici bir SMS/Webhook API'sine iletmek üzere tasarlanmıştır.

## ✨ Temel Mimari

* **Çerçeve:** Laravel (PHP)
* **Veritabanı:** SQLite (Geliştirme için)
* **Kuyruk Sürücüsü/Cache:** Redis
* **Design Pattern:** Service-Repository
* **Özellikler:** Rate Limiting (Hız Sınırlama), Queue/Job yapısı, Swagger API Dokümantasyonu.

## 🛠️ Kurulum ve Çalıştırma

Bu projenin çalışması için **PHP**, **Composer** ve **Redis** sunucusunun aktif olması gerekir.

### 1. Klonlama ve Bağımlılıklar

Projeyi klonlayın ve Composer bağımlılıklarını kurun:


# GitHub'dan klonlayın
git clone https://github.com/oguzhanemet/bulkMessagingJob.git
cd bulkMessagingJob/messagesTaskOguzhanE

# PHP bağımlılıklarını kurun
composer install

2. Çevre Değişkenleri (Environment)
.env dosyasını oluşturun ve temel konfigürasyonları yapın:

cp .env.example .env

.env dosyanızı açın ve aşağıdaki temel ayarların yapıldığından emin olun:
| Değişken Adı | Değer | Açıklama |
| :--- | :--- | :--- |
| `DB_CONNECTION` | `sqlite` | Veritabanı sürücüsü |
| `QUEUE_CONNECTION` | `redis` | Asenkron kuyruk için Redis kullanılır |
| `REDIS_CLIENT` | `predis` veya `phpredis` | Tercihe göre Redis istemcisi |
| `WEBHOOK_SITE_URL` | `[Sizin Webhook URL'niz]` | Mesajın gönderileceği harici API adresi |
| `WEBHOOK_API_KEY` | `[Sizin Auth Key'iniz]` | `x-ins-auth-key` başlığı için kullanılan kimlik anahtarı |

3. Veritabanı ve Redis Kurulumu
a) SQLite Veritabanı Oluşturma
SQLite kullanıldığı için boş bir veritabanı dosyası oluşturulmalıdır:

touch database/database.sqlite

b) Migrationları Çalıştırma
Mesajlar tablosunu oluşturun:

php artisan migrate

c) Redis Sunucusunu Başlatma
Yerel Redis sunucunuzun arka planda çalıştığından emin olun (Örn: Docker veya Redis Desktop Manager kullanarak).

4. Uygulamayı Başlatma
Projenin tam olarak çalışması için 3 ayrı terminal penceresi açılması gerekir:

Terminal 1: Laravel Web Sunucusu

php artisan serve
(API isteklerini karşılayacak)

Terminal 2: Kuyruk İşçisi (Queue Worker)
Bu, Redis kuyruğundaki mesajları çekecek ve harici Webhook'a isteği atacaktır.

php artisan queue:work
(Asenkron gönderme işlemini yapacak. Bu olmadan mesajlar gönderilmez.)

Terminal 3: Scheduler (Planlanmış Görevler)
(Opsiyonel olarak, eğer cron job tanımlanmışsa)

php artisan schedule:run

📡 API Kullanımı
API'ye erişim http://localhost:8000/api/ üzerinden sağlanır.

1. API Dokümantasyonu (Swagger)
Tüm endpoint'ler ve beklenen parametreler Swagger arayüzünde görülebilir. (Dokümantasyon, son geliştirme adımı olarak l5-swagger:generate komutuyla oluşturulmuştur.)

Adres: http://localhost:8000/api/documentation

1.2. Mesaj Listeleme Endpoint Tablosu


| Metod | Endpoint | Açıklama |
| :--- | :--- | :--- |
| `GET` | `/api/messages` | Durumu `sent` olan mesajları listeler. |


