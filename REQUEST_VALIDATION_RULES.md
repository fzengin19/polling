# Tüm Request Sınıfları ve Validasyon Kuralları

---

## Auth\LoginRequest
```
'email'    => 'required|string|email',
'password' => 'required|string',
```

---

## Auth\RegisterRequest
```
'name'     => 'required|string|max:255',
'email'    => 'required|string|email|max:255|unique:users',
'password' => 'required|string|min:8|confirmed',
```

---

## Auth\GoogleLoginRequest
```
'google_access_token' => 'required|string|max:1000',
```

---

## Role\AssignRoleRequest
```
'role_name'  => 'required|string|exists:roles,name',
'model_type' => ['required', 'string', Rule::in(['user', 'survey'])],
'model_id'   => 'required|integer',
```

---

## Role\RemoveRoleRequest
```
'user_id' => 'required|integer|exists:users,id',
'role_name' => 'required|string|exists:roles,name',
```

---

## Media\UploadMediaRequest
```
'model_type'      => 'required|string|in:question,survey,survey-page,choice',
'model_id'        => 'required|integer|min:1',
'collection_name' => 'required|string|max:255',
'file'            => 'required|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx|max:10240',
'alt_text'        => 'sometimes|string|max:255',
'caption'         => 'sometimes|string|max:500',
```

---

## Media\EnhancedUploadMediaRequest
```
'file'             => 'required|file|max:10240',
'model_type'       => 'required|string|in:question,choice,survey,survey-page',
'model_id'         => 'required|integer',
'collection_name'  => 'required|string',
'custom_properties'=> 'nullable|array',
```

---

## Media\UpdateMediaMetadataRequest
```
'name'             => 'sometimes|string|max:255',
'custom_properties'=> 'sometimes|array',
```

---

## User\UpdateProfileRequest
```
'name'             => 'sometimes|required|string|max:255',
'email'            => [
    'sometimes',
    'required',
    'email',
    'max:255',
    Rule::unique('users')->ignore(Auth::id()),
],
'experience_level' => 'sometimes|required|string|in:basic,intermediate,advanced',
```

---

## Survey\CreateSurveyRequest
```
'title'                        => 'required|string|max:255',
'description'                  => 'nullable|string|max:1000',
'status'                       => 'required|in:draft,active,archived',
'template_id'                  => 'nullable|integer|min:1',
'template_version_id'          => 'nullable|integer|min:1',
'settings'                     => 'nullable|array',
'settings.ui_complexity_level' => 'nullable|string|in:basic,intermediate,advanced',
'expires_at'                   => 'nullable|date|after:now',
'max_responses'                => 'nullable|integer|min:1|max:1000000',
'settings.theme'               => 'nullable|array',
'settings.theme.primary_color' => ['nullable', 'string', 'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'],
'settings.theme.font'          => ['nullable', 'string', Rule::in(['Arial', 'Georgia', 'Lato', 'Roboto', 'Verdana'])],
'settings.theme.logo_media_id' => 'nullable|integer|exists:media,id',
'settings.theme.logo_placement'=> ['nullable', 'string', Rule::in(['top', 'bottom', 'top-left', 'top-right', 'bottom-left', 'bottom-right'])],
'settings.theme.background_color'=> ['nullable', 'string', 'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'],
```

---

## Survey\UpdateSurveyRequest
```
'title'                        => 'sometimes|string|max:255',
'description'                  => 'nullable|string',
'status'                       => ['sometimes', 'string', Rule::in(['draft', 'active', 'archived'])],
'settings'                     => 'sometimes|array',
'settings.theme.primary_color' => ['sometimes', 'string', 'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'],
'settings.theme.font'          => ['sometimes', 'string', Rule::in(['Arial', 'Georgia', 'Lato', 'Roboto', 'Verdana'])],
'settings.theme.logo_media_id' => 'nullable|integer|exists:media,id',
'settings.theme.logo_placement'=> ['sometimes', 'string', Rule::in(['top', 'bottom', 'top-left', 'top-right', 'bottom-left', 'bottom-right'])],
'settings.theme.background_color'=> ['sometimes', 'string', 'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'],
```

---

## SurveyPage\CreateSurveyPageRequest
```
'survey_id'   => 'required|integer|exists:surveys,id',
'title'       => 'required|string|max:255',
'description' => 'nullable|string|max:1000',
'order'       => 'nullable|integer|min:0',
```

---

## SurveyPage\UpdateSurveyPageRequest
```
'title'       => 'sometimes|string|max:255',
'description' => 'nullable|string|max:1000',
'order'       => 'sometimes|integer|min:0',
```

---

## SurveyPage\ReorderSurveyPagesRequest
```
'page_ids'   => 'required|array',
'page_ids.*' => 'integer|exists:survey_pages,id',
```

---

## Response\CreateResponseRequest
```
'survey_id' => 'required|integer|exists:surveys,id',
```

---

## Response\SubmitResponseRequest
```
'answers'                => 'required|array',
'answers.*.question_id'  => 'required|integer|exists:questions,id',
'answers.*.value'        => 'required|string',
```

---

## Choice\CreateChoiceRequest
```
'label'       => 'required|string|max:255',
'value'       => 'required|string|max:255',
'order_index' => 'integer|min:0',
```

---

## Choice\UpdateChoiceRequest
```
'label'       => 'sometimes|string|max:255',
'value'       => 'sometimes|string|max:255',
'order_index' => 'sometimes|integer|min:0',
```

---

## Question\CreateQuestionRequest
```
'type'        => 'required|string|max:50',
'title'       => 'required|string|max:255',
'is_required' => 'boolean',
'help_text'   => 'nullable|string|max:255',
'placeholder' => 'nullable|string|max:255',
'config'      => 'nullable|array',
'order_index' => 'integer|min:0',
// + type'a göre conditional config kuralları (ör: number, linear_scale)
```

---

## Question\UpdateQuestionRequest
```
'type'        => 'sometimes|string|max:50',
'title'       => 'sometimes|string|max:255',
'is_required' => 'sometimes|boolean',
'help_text'   => 'nullable|string|max:255',
'placeholder' => 'nullable|string|max:255',
'config'      => 'sometimes|array',
'order_index' => 'sometimes|integer|min:0',
// + type'a göre conditional config kuralları (ör: number, linear_scale)
```

---

## Template\CreateTemplateRequest
```
'name'        => 'required|string|max:255',
'description' => 'nullable|string|max:1000',
'category'    => 'required|string|max:100',
```

---

## Template\UpdateTemplateRequest
```
'name'        => 'sometimes|string|max:255',
'description' => 'nullable|string|max:1000',
'category'    => 'sometimes|string|max:100',
``` 