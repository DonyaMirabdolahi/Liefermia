<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Items List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .image-loading {
            position: relative;
            background: #f3f4f6;
            min-height: 120px;
        }
        .image-loading::after {
            content: "Loading...";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #6b7280;
        }
        .image-error {
            position: relative;
            background: #f3f4f6;
            min-height: 120px;
        }
        .image-error::after {
            content: "Image not available";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #6b7280;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Available Items</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($items as $item)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="p-4">
                        <h2 class="text-xl font-semibold mb-2">{{ $item['name'] }}</h2>
                        <div class="flex items-center space-x-4">
                            <div class="w-24 h-24 relative image-loading flex-shrink-0">
                                <img 
                                    src="{{ asset($item['image_url']) }}" 
                                    alt="{{ $item['name'] }}"
                                    class="w-full h-full object-cover rounded-lg"
                                    loading="lazy"
                                    onload="this.parentElement.classList.remove('image-loading')"
                                    onerror="this.parentElement.classList.add('image-error'); this.parentElement.classList.remove('image-loading'); this.style.display='none';"
                                >
                            </div>
                            <div class="flex-grow">
                                <p class="text-gray-600 text-sm mb-1">ID: {{ $item['id'] }}</p>
                                <p class="text-green-600 font-semibold text-lg">
                                    From ${{ number_format($item['min_price'], 2) }}
                                </p>
                            </div>
                        </div>
                        <a 
                            href="{{ url('/items/' . $item['id']) }}" 
                            class="mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors w-full text-center"
                            target="_blank"
                        >
                            View Details
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html> 