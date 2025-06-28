<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>React Pizza App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div id="app"></div>

    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const appElement = document.getElementById('app');
            
            if (!appElement) {
                console.error('App element not found');
                return;
            }

            if (typeof React === 'undefined') {
                console.error('React is not loaded');
                return;
            }

            if (typeof ReactDOM === 'undefined') {
                console.error('ReactDOM is not loaded');
                return;
            }

            const App = window.App;
            if (!App) {
                console.error('App component not found');
                return;
            }

            ReactDOM.render(React.createElement(App), appElement);
        });
    </script>
</body>
</html>
