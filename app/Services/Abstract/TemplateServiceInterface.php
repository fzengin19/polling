<?php

namespace App\Services\Abstract;

use App\Dtos\TemplateDto;
use App\Models\Template;
use App\Responses\ServiceResponse;

interface TemplateServiceInterface
{
    public function create(TemplateDto $dto): ServiceResponse;
    public function update(int $id, TemplateDto $dto): ServiceResponse;
    public function delete(int $id): ServiceResponse;
    public function find(int $id): ServiceResponse;
    public function getAll(): ServiceResponse;
    public function getByUser(int $userId): ServiceResponse;
    public function getPublicTemplates(): ServiceResponse;
    public function fork(int $templateId, int $userId): ServiceResponse;
    
    // Version Management
    public function getVersions(int $templateId): ServiceResponse;
    public function createVersion(int $templateId, int $userId): ServiceResponse;
    public function restoreVersion(int $templateId, int $versionId, int $userId): ServiceResponse;
} 