<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'notification_type_id',
        'name',
        'subject',
        'body_html',
        'body_text',
        'available_variables',
        'locale',
        'is_active',
        'is_default',
        'version',
        'preview_data',
    ];

    protected $casts = [
        'available_variables' => 'array',
        'preview_data' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'version' => 'integer',
    ];

    // Relationships
    public function notificationType()
    {
        return $this->belongsTo(NotificationType::class);
    }

    public function notificationLogs()
    {
        return $this->hasMany(NotificationLog::class);
    }

    // Scopes
    #[Scope]
    public function active($query)
    {
        return $query->where('is_active', true);
    }

    #[Scope]
    public function default($query)
    {
        return $query->where('is_default', true);
    }

    #[Scope]
    public function locale($query, $locale)
    {
        return $query->where('locale', $locale);
    }

    // Helper methods
    public function render($data)
    {
        $subject = $this->replaceVariables($this->subject, $data);
        $bodyHtml = $this->replaceVariables($this->body_html, $data);
        $bodyText = $this->body_text ? $this->replaceVariables($this->body_text, $data) : null;

        return [
            'subject' => $subject,
            'body_html' => $bodyHtml,
            'body_text' => $bodyText,
        ];
    }

    private function replaceVariables($template, $data)
    {
        foreach ($data as $key => $value) {
            // Handle nested arrays/objects
            if (is_array($value) || is_object($value)) {
                continue;
            }
            $template = str_replace('{{' . $key . '}}', $value, $template);
            $template = str_replace('{{ ' . $key . ' }}', $value, $template);
        }
        return $template;
    }

    public function getPreviewHtml($data = null)
    {
        $previewData = $data ?? ($this->preview_data ?: []);
        $rendered = $this->render($previewData);
        return $rendered['body_html'];
    }
}
