document.addEventListener('DOMContentLoaded', function() {
    const banner = document.querySelector('.cookie-banner');
    if (!banner) {
        return; // Если баннера нет на странице, ничего не делаем
    }

    const COOKIE_NAME = 'cookie_policy_accepted_time';
    const policyResetTime = parseInt(banner.getAttribute('data-reset-timestamp'), 10) || 0;
    const userAcceptedTime = parseInt(getCookie(COOKIE_NAME), 10) || 0;

    // Основная логика: показывать баннер, если пользователь не соглашался
    // ИЛИ если админ сбросил политику ПОСЛЕ того, как пользователь согласился.
    if (userAcceptedTime < policyResetTime) {
        banner.style.display = 'block';
    }

    // Обработчик для кнопки "Принять"
    const acceptButton = banner.querySelector('.cookie-btn');
    if (acceptButton) {
        acceptButton.addEventListener('click', function() {
            // Устанавливаем cookie с текущим временем на 1 год
            const currentTime = Math.floor(Date.now() / 1000);
            setCookie(COOKIE_NAME, currentTime, 365);
            banner.style.display = 'none';
        });
    }

    // Закрытие модального окна
    const closeButton = banner.querySelector('.cookie-close-btn');
    if (closeButton) {
        closeButton.addEventListener('click', function() {
            banner.style.display = 'none';
        });
    }

    // --- Вспомогательные функции для работы с cookie ---
    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }

    function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
});