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


## 🛠️ Kurulum ve Çalıştırma (Devam)

### 3. Veritabanı ve Redis Kurulumu

Bu adımlar, projenin veritabanını hazırlar ve Redis kuyruk bağlantısını kurar.

#### a) SQLite Veritabanı Oluşturma

SQLite kullanıldığı için boş bir veritabanı dosyası oluşturulmalıdır:


touch database/database.sqlite
b) Migrationları Çalıştırma
Mesajlar tablosunu oluşturun. (Lütfen messagesTaskOguzhanE dizini içinde olduğunuzdan emin olun):


php artisan migrate
c) Redis Sunucusunu Başlatma
Yerel Redis sunucunuzun arka planda çalıştığından emin olun (Örn: Docker veya Redis Desktop Manager kullanarak). Redis, kuyruk ve Rate Limiting için gereklidir.

4. Uygulamayı Başlatma
Projenin tam olarak çalışması ve mesaj gönderiminin gerçekleşmesi için 3 ayrı terminal penceresi açılması gerekir:

🟢 Terminal 1: Laravel Web Sunucusu
API isteklerini karşılayacak ana sunucuyu başlatır:


php artisan serve
🟡 Terminal 2: Veri Ekleme ve Kuyruğa Gönderme (Seed/Dispatch)
Bu terminal, başlangıç verilerini SQLite veritabanına eklemek ve bu verileri kuyruğa atmak için kullanılır.

Veri Ekleme (Seeding): İçeride belirtilen miktarda veriyi SQLite veritabanına ekler.


php artisan db:seed --class=MessageSeeder
Kuyruğa Gönderme (Dispatch): Eklenen bu verileri işlenmek üzere Redis Kuyruğu'na gönderir.


php artisan messages:dispatch

🔴 Terminal 3: Kuyruk İşçisi (Queue Worker)
Bu, Redis kuyruğundaki mesajları çekecek ve Rate Limiting kurallarına uyarak harici Webhook'a isteği atacaktır.


php artisan queue:work
(UYARI: Bu komut çalışmadan mesajlar gönderilmez ve WebHook'a ulaşmaz.)

⚙️ Ek İşlemler ve Notlar
Hız Sınırlama (Rate Limiting) Ayarı
Proje, 5 saniyede 2 mesaj gönderilme isteğini karşılamak üzere dakikada 24 mesaj gönderilme isteği olarak düzenlenmiştir (AppServiceProvider içinde tanımlanmıştır).

WebHook Kontrolü
Webhook paneline (Curl örneğinde belirtilen formatta) başarılı (202 Accepted) istekler ve veriler eklenecektir.

Temiz Başlangıç
Tüm veritabanı tablolarını silip migrationları yeniden çalıştırmak ve seeder verilerini tekrar eklemek için:


php artisan migrate:fresh --seed
Gönderilmiş Mesajları Tekrar Göndermeme (Status Kontrolü)
Sistem, sadece veritabanında status sütunu pending olan mesajları gönderir. sent olanlar göz ardı edilir.

Manuel Test Verisi Ekleme: Gönderilmeyi bekleyen yeni veriler ekleyip queue:work ile test etmek için tinker kullanabilirsiniz:


php artisan tinker
>>> App\Models\Message::create(['recipient' => '+905559999999', 'content' => 'Yeni Test Mesajı 1', 'status' => 'pending']);
>>> App\Models\Message::create(['recipient' => '+905558888888', 'content' => 'Yeni Test Mesajı 2', 'status' => 'pending']);
>>> exit
Yeni veriler eklendikten sonra php artisan queue:work tekrar çalıştırıldığı zaman, sadece yeni eklenen (pending) verilerin işlendiği görülecektir.


📡 API Kullanımı
API'ye erişim http://localhost:8000/api/ üzerinden sağlanır.

1. API Dokümantasyonu (Swagger)
Tüm endpoint'ler ve beklenen parametreler Swagger arayüzünde görülebilir. (Dokümantasyon, son geliştirme adımı olarak l5-swagger:generate komutuyla oluşturulmuştur.)

Adres: http://localhost:8000/api/documentation

1.2. Mesaj Listeleme Endpoint Tablosu


| Metod | Endpoint | Açıklama |
| :--- | :--- | :--- |
| `GET` | `/api/messages` | Durumu `sent` olan mesajları listeler. |

Adres: http://localhost:8000/api/messages

