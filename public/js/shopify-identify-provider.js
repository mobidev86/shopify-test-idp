/**
 * Shopify Identify Provider Button
 * 
 * This script adds an "Identify" button to the Shopify customer list.
 * When clicked, it redirects to the identify provider endpoint with the customer's email.
 */

(function() {
    // Configuration
    const config = {
        identifyProviderUrl: 'https://your-app-domain.com/identify', // Replace with your actual domain
        buttonText: 'Identify',
        buttonClass: 'identify-provider-btn',
    };

    // Function to add the identify button to the customer list
    function addIdentifyButton() {
        // Find the customer list table
        const customerTable = document.querySelector('table.customer-list');
        if (!customerTable) return;

        // Find the header row
        const headerRow = customerTable.querySelector('thead tr');
        if (!headerRow) return;

        // Add a new header cell for the identify button
        const headerCell = document.createElement('th');
        headerCell.textContent = 'Identify';
        headerCell.className = 'identify-provider-header';
        headerRow.appendChild(headerCell);

        // Find all customer rows
        const customerRows = customerTable.querySelectorAll('tbody tr');
        
        // Add the identify button to each customer row
        customerRows.forEach(row => {
            // Find the email cell
            const emailCell = row.querySelector('td:nth-child(2)'); // Adjust the index based on your table structure
            if (!emailCell) return;
            
            const email = emailCell.textContent.trim();
            
            // Create a new cell for the identify button
            const buttonCell = document.createElement('td');
            buttonCell.className = 'identify-provider-cell';
            
            // Create the identify button
            const button = document.createElement('button');
            button.textContent = config.buttonText;
            button.className = config.buttonClass;
            button.addEventListener('click', () => handleIdentifyClick(email));
            
            // Add the button to the cell
            buttonCell.appendChild(button);
            
            // Add the cell to the row
            row.appendChild(buttonCell);
        });
    }

    // Function to handle the identify button click
    function handleIdentifyClick(email) {
        // Get the current shop domain
        const shop = Shopify.shop;
        
        // Generate a timestamp
        const timestamp = Math.floor(Date.now() / 1000).toString();
        
        // Create the parameters for the identify provider request
        const params = {
            shop: shop,
            email: email,
            timestamp: timestamp,
            return_to: window.location.href,
        };
        
        // Sort the parameters alphabetically
        const sortedParams = Object.keys(params).sort().reduce((acc, key) => {
            acc[key] = params[key];
            return acc;
        }, {});
        
        // Create the query string
        const queryString = Object.entries(sortedParams)
            .map(([key, value]) => `${encodeURIComponent(key)}=${encodeURIComponent(value)}`)
            .join('&');
        
        // Create the HMAC signature
        // Note: In a real implementation, this would be done server-side
        // This is just a placeholder
        const hmac = 'PLACEHOLDER_HMAC';
        
        // Add the HMAC to the parameters
        params.hmac = hmac;
        
        // Create the final URL
        const url = `${config.identifyProviderUrl}?${Object.entries(params)
            .map(([key, value]) => `${encodeURIComponent(key)}=${encodeURIComponent(value)}`)
            .join('&')}`;
        
        // Redirect to the identify provider
        window.location.href = url;
    }

    // Add some basic styles
    const style = document.createElement('style');
    style.textContent = `
        .identify-provider-btn {
            background-color: #008060;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 6px 12px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .identify-provider-btn:hover {
            background-color: #006e52;
        }
        
        .identify-provider-header {
            text-align: center;
            font-weight: bold;
        }
        
        .identify-provider-cell {
            text-align: center;
        }
    `;
    document.head.appendChild(style);

    // Initialize the identify provider button
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', addIdentifyButton);
    } else {
        addIdentifyButton();
    }
})(); 