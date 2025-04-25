<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Welcome, {{ Auth::user()->name }}!</h1>
                    
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif
                    
                    <!-- Shopify Connection Section -->
                    <div class="mt-6 mb-8">
                        <h2 class="text-xl font-semibold mb-4">Shopify Connection</h2>
                        
                        @if(Session::has('shopify_access_token'))
                            <div class="bg-green-50 p-4 rounded-lg mb-4">
                                <p class="text-green-800">Connected to Shopify store: <strong>{{ Session::get('shopify_shop') }}</strong></p>
                                <button id="test-api" class="mt-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Test API Connection
                                </button>
                                <div id="api-result" class="mt-4 p-4 bg-gray-100 rounded hidden">
                                    <pre class="text-sm overflow-auto"></pre>
                                </div>
                            </div>
                        @else
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="mb-4">Connect your Shopify store to enable OAuth authentication.</p>
                                <form action="{{ route('shopify.connect') }}" method="GET" class="flex items-center">
                                    <input type="text" name="shop" placeholder="your-store.myshopify.com" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 mr-2" required>
                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                        Connect Store
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                    
                    <!-- OAuth Clients Section -->
                    <div class="mt-4">
                        <h2 class="text-xl font-semibold mb-2">Your OAuth Clients</h2>
                        <div id="oauth-clients" class="space-y-4">
                            <!-- OAuth clients will be loaded here -->
                        </div>
                    </div>

                    <div class="mt-6">
                        <h2 class="text-xl font-semibold mb-2">Create New OAuth Client</h2>
                        <form id="create-client-form" class="space-y-4">
                            <div>
                                <label for="client-name" class="block text-sm font-medium text-gray-700">Client Name</label>
                                <input type="text" id="client-name" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label for="redirect" class="block text-sm font-medium text-gray-700">Redirect URI</label>
                                <input type="text" id="redirect" name="redirect" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Create Client
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Load OAuth clients
        async function loadClients() {
            try {
                const response = await fetch('/oauth/clients', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const clients = await response.json();
                const clientsContainer = document.getElementById('oauth-clients');
                clientsContainer.innerHTML = clients.map(client => `
                    <div class="border p-4 rounded-lg">
                        <h3 class="font-semibold">${client.name}</h3>
                        <p class="text-sm text-gray-600">Client ID: ${client.id}</p>
                        <p class="text-sm text-gray-600">Secret: ${client.secret}</p>
                        <p class="text-sm text-gray-600">Redirect: ${client.redirect}</p>
                    </div>
                `).join('');
            } catch (error) {
                console.error('Error loading clients:', error);
            }
        }

        // Create new OAuth client
        document.getElementById('create-client-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            try {
                const response = await fetch('/oauth/clients', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        name: formData.get('name'),
                        redirect: formData.get('redirect')
                    })
                });
                if (response.ok) {
                    loadClients();
                    e.target.reset();
                }
            } catch (error) {
                console.error('Error creating client:', error);
            }
        });

        // Test Shopify API connection
        document.getElementById('test-api')?.addEventListener('click', async () => {
            const resultElement = document.getElementById('api-result');
            const preElement = resultElement.querySelector('pre');
            
            try {
                const response = await fetch('{{ route("shopify.api") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        endpoint: '/admin/api/2023-01/shop.json',
                        method: 'GET'
                    })
                });
                
                const data = await response.json();
                preElement.textContent = JSON.stringify(data, null, 2);
                resultElement.classList.remove('hidden');
            } catch (error) {
                preElement.textContent = `Error: ${error.message}`;
                resultElement.classList.remove('hidden');
            }
        });

        // Load clients on page load
        loadClients();
    </script>
    @endpush
</x-app-layout> 