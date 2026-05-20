<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <h5>Современная гуманитаристика</h5>
                <p style="opacity: 0.7;">Научный журнал</p>
                <p style="opacity: 0.6; font-size: 0.9rem;">
                    ISSN: 3034-6827 (Print)<br> 
                    ISSN: 3033-8638 (Online)
                </p>

                <div class="social-links">
                    <!-- Telegram -->
                    <a href="#" aria-label="Telegram"><i class="bi bi-telegram"></i></a>

                    <!-- ВКонтакте (VK) -->
                    <a href="#" aria-label="VK"><i class="fab fa-vk"></i></a>

                    <!-- Мессенджер Max (используем универсальную иконку чата) -->

                    <a href="#" aria-label="Max"><i class="fas fa-comment-dots"></i></a>

                </div>
            </div>

            <div class="col-lg-2 mb-4">
                <h5>Разделы</h5>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}"><i class="bi bi-chevron-right"></i> Главная</a></li>
                    <li><a href="{{ route('home') }}#about"><i class="bi bi-chevron-right"></i> О журнале</a></li>
                    <li><a href="{{ route('home') }}#archive"><i class="bi bi-chevron-right"></i> Архив</a></li>
                    <li><a href="{{ route('home') }}#for-authors"><i class="bi bi-chevron-right"></i> Авторам</a></li>
                    <li><a href="{{ route('home') }}#contacts"><i class="bi bi-chevron-right"></i> Контакты</a></li>
                </ul>
            </div>

            <div class="col-lg-4 mb-4">
                <h5>Контакты</h5>
                <ul class="footer-links">
                    <li><i class="bi bi-geo-alt"></i> 428015, Чувашская Республика, город Чебоксары, Московский проспект, 29, корпус 1.</li>
                    <li><i class="bi bi-envelope"></i> sovrem_human@rambler.ru</li>
                    <li><i class="bi bi-telephone"></i> 8(8352) 45-00-10</li>
                </ul>
            </div>
        </div>

        <div class="copyright">
            © {{ date('Y') }} Современная гуманитаристика. Все права защищены.
        </div>
    </div>
</footer>