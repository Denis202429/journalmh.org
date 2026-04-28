<div class="row g-3">
    <div class="col-12">
        <label class="form-label">Библиографическая запись (текст)</label>
        <textarea name="references[{{ $index }}][text]" class="form-control" rows="3" placeholder="ГОСТ Р 7.0.5-2008 или APA">{{ $reference['text'] ?? '' }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label">DOI</label>
        <input type="text" name="references[{{ $index }}][doi]" class="form-control" value="{{ $reference['doi'] ?? '' }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">URL</label>
        <input type="url" name="references[{{ $index }}][url]" class="form-control" value="{{ $reference['url'] ?? '' }}">
    </div>
</div>