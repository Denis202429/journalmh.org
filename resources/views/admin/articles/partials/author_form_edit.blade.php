<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Фамилия (RU) *</label>
        <input type="text" name="authors[{{ $index }}][surname_ru]" class="form-control" 
               value="{{ is_array($author) ? ($author['surname_ru'] ?? '') : ($author->surname_ru ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Фамилия (EN) *</label>
        <input type="text" name="authors[{{ $index }}][surname_en]" class="form-control" 
               value="{{ is_array($author) ? ($author['surname_en'] ?? '') : ($author->surname_en ?? '') }}" required>
    </div>
    
    <div class="col-md-4">
        <label class="form-label">Инициалы (RU)</label>
        <input type="text" name="authors[{{ $index }}][initials_ru]" class="form-control" 
               value="{{ is_array($author) ? ($author['initials_ru'] ?? '') : ($author->initials_ru ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Инициалы (EN)</label>
        <input type="text" name="authors[{{ $index }}][initials_en]" class="form-control" 
               value="{{ is_array($author) ? ($author['initials_en'] ?? '') : ($author->initials_en ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">ORCID</label>
        <input type="text" name="authors[{{ $index }}][orcid]" class="form-control" 
               value="{{ is_array($author) ? ($author['orcid'] ?? '') : ($author->orcid ?? '') }}" placeholder="0000-0000-0000-0000">
    </div>
    
    <div class="col-md-4">
        <label class="form-label">SPIN</label>
        <input type="text" name="authors[{{ $index }}][spin]" class="form-control" 
               value="{{ is_array($author) ? ($author['spin'] ?? '') : ($author->spin ?? '') }}" placeholder="1234-5678">
    </div>
    <div class="col-md-4">
        <label class="form-label">Email</label>
        <input type="email" name="authors[{{ $index }}][email]" class="form-control" 
               value="{{ is_array($author) ? ($author['email'] ?? '') : ($author->email ?? '') }}">
    </div>
    <div class="col-md-4">
        <div class="form-check mt-4">
            <input type="checkbox" name="authors[{{ $index }}][is_correspondent]" class="form-check-input" value="1"
                   {{ (is_array($author) ? ($author['is_correspondent'] ?? false) : ($author->is_correspondent ?? false)) ? 'checked' : '' }}>
            <label class="form-check-label">Автор-корреспондент</label>
        </div>
    </div>
    
    <div class="col-md-12">
        <label class="form-label">Организация (RU)</label>
        <input type="text" name="authors[{{ $index }}][org_name_ru]" class="form-control" 
               value="{{ is_array($author) ? ($author['org_name_ru'] ?? '') : ($author->org_name_ru ?? '') }}">
    </div>
    <div class="col-md-12">
        <label class="form-label">Организация (EN)</label>
        <input type="text" name="authors[{{ $index }}][org_name_en]" class="form-control" 
               value="{{ is_array($author) ? ($author['org_name_en'] ?? '') : ($author->org_name_en ?? '') }}">
    </div>
    
    <div class="col-md-6">
        <label class="form-label">Город (RU)</label>
        <input type="text" name="authors[{{ $index }}][town_ru]" class="form-control" 
               value="{{ is_array($author) ? ($author['town_ru'] ?? '') : ($author->town_ru ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Страна (RU)</label>
        <input type="text" name="authors[{{ $index }}][country_ru]" class="form-control" 
               value="{{ is_array($author) ? ($author['country_ru'] ?? '') : ($author->country_ru ?? '') }}">
    </div>
</div>