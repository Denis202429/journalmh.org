<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Фамилия (RU) *</label>
        <input type="text" name="authors[{{ $index }}][surname_ru]" class="form-control" value="{{ $author['surname_ru'] ?? '' }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Фамилия (EN) *</label>
        <input type="text" name="authors[{{ $index }}][surname_en]" class="form-control" value="{{ $author['surname_en'] ?? '' }}" required>
    </div>
    
    <div class="col-md-4">
        <label class="form-label">Инициалы (RU)</label>
        <input type="text" name="authors[{{ $index }}][initials_ru]" class="form-control" value="{{ $author['initials_ru'] ?? '' }}" placeholder="И.И.">
    </div>
    <div class="col-md-4">
        <label class="form-label">Инициалы (EN)</label>
        <input type="text" name="authors[{{ $index }}][initials_en]" class="form-control" value="{{ $author['initials_en'] ?? '' }}" placeholder="I.I.">
    </div>
    <div class="col-md-4">
        <label class="form-label">Роль (role)</label>
        <select name="authors[{{ $index }}][role]" class="form-select">
            <option value="">Автор</option>
            <option value="0" {{ ($author['role'] ?? '') == '0' ? 'selected' : '' }}>Редактор</option>
            <option value="23" {{ ($author['role'] ?? '') == '23' ? 'selected' : '' }}>Рецензент</option>
        </select>
    </div>
    
    <div class="col-md-3">
        <label class="form-label">ORCID</label>
        <input type="text" name="authors[{{ $index }}][orcid]" class="form-control" value="{{ $author['orcid'] ?? '' }}" placeholder="0000-0000-0000-0000">
    </div>
    <div class="col-md-3">
        <label class="form-label">SPIN</label>
        <input type="text" name="authors[{{ $index }}][spin]" class="form-control" value="{{ $author['spin'] ?? '' }}" placeholder="1234-5678">
    </div>
    <div class="col-md-3">
        <label class="form-label">ResearcherID</label>
        <input type="text" name="authors[{{ $index }}][researcherid]" class="form-control" value="{{ $author['researcherid'] ?? '' }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">ScopusID</label>
        <input type="text" name="authors[{{ $index }}][scopusid]" class="form-control" value="{{ $author['scopusid'] ?? '' }}">
    </div>
    
    <div class="col-md-12">
        <label class="form-label">Организация (RU)</label>
        <input type="text" name="authors[{{ $index }}][org_name_ru]" class="form-control" value="{{ $author['org_name_ru'] ?? '' }}">
    </div>
    <div class="col-md-12">
        <label class="form-label">Организация (EN)</label>
        <input type="text" name="authors[{{ $index }}][org_name_en]" class="form-control" value="{{ $author['org_name_en'] ?? '' }}">
    </div>
    
    <div class="col-md-4">
        <label class="form-label">Город (RU)</label>
        <input type="text" name="authors[{{ $index }}][town_ru]" class="form-control" value="{{ $author['town_ru'] ?? '' }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Страна (RU)</label>
        <input type="text" name="authors[{{ $index }}][country_ru]" class="form-control" value="{{ $author['country_ru'] ?? '' }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Email</label>
        <input type="email" name="authors[{{ $index }}][email]" class="form-control" value="{{ $author['email'] ?? '' }}">
    </div>
    
    <div class="col-md-6">
        <div class="form-check">
            <input type="checkbox" name="authors[{{ $index }}][is_correspondent]" class="form-check-input" value="1" {{ ($author['is_correspondent'] ?? false) ? 'checked' : '' }}>
            <label class="form-check-label">Автор-корреспондент</label>
        </div>
    </div>
</div>