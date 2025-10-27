<?php

namespace App\Repositories;

interface MessageRepositoryInterface
{
    public function getPendingMessages(int $limit);
    public function markAsSent(int $messageId, string $externalId);
    public function markAsFailed(int $messageId);
    public function getSentMessages();
}