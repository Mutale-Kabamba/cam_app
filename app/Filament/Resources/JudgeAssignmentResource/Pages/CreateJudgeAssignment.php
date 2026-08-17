<?php

namespace App\Filament\Resources\JudgeAssignmentResource\Pages;

use App\Filament\Resources\JudgeAssignmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateJudgeAssignment extends CreateRecord
{
    protected static string $resource = JudgeAssignmentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
