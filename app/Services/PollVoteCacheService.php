<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class PollVoteCacheService
{
    protected $prefix = 'pollvotes:';

    // ID bazlı key oluşturur
    protected function key($id)
    {
        return $this->prefix . $id;
    }

    // Yeni pollvote ekle (ID otomatik UUID atanıyor)
    public function create(array $data)
    {
        $id = (string) Str::uuid();

        $data['id'] = $id;

        // Redis'e JSON string olarak kaydet
        Redis::set($this->key($id), json_encode($data));

        // İstersen tüm ID'leri listeleyen set ekleyebilirsin
        Redis::sadd($this->prefix . 'ids', $id);

        return $data;
    }

    // ID ile getir
    public function find(string $id)
    {
        $json = Redis::get($this->key($id));

        if (!$json) {
            return null;
        }

        return json_decode($json, true);
    }

    // Güncelleme
    public function update(string $id, array $data)
    {
        $existing = $this->find($id);

        if (!$existing) {
            return null;
        }

        $updated = array_merge($existing, $data);

        Redis::set($this->key($id), json_encode($updated));

        return $updated;
    }

    // Silme
    public function delete(string $id)
    {
        Redis::del($this->key($id));
        Redis::srem($this->prefix . 'ids', $id);
        return true;
    }

    // Tüm kayıtları getir (performans gerektirir, küçük veri için uygun)
    public function all()
    {
        $ids = Redis::smembers($this->prefix . 'ids');

        $all = [];

        foreach ($ids as $id) {
            $all[] = $this->find($id);
        }

        return $all;
    }
}
