function toggleFavorite(event, icon) {
    event.preventDefault(); // Prevents default action of the link

    // Find the property details
    const property = icon.closest('.property-item');
    const propertyData = {
        image: property.querySelector('.property-img img').src,
        title: property.querySelector('h3 a').textContent,
        location: property.querySelector('p').textContent,
        price: property.querySelector('.property-price span').textContent,
        link: property.querySelector('h3 a').href
    };

    // Retrieve favorites from local storage
    let favorites = JSON.parse(localStorage.getItem('favorites')) || [];
    const isFavorite = favorites.some(fav => fav.title === propertyData.title);

    // Toggle favorite status
    if (isFavorite) {
        favorites = favorites.filter(fav => fav.title !== propertyData.title);
        icon.querySelector('i').classList.replace('bi-heart-fill', 'bi-heart');
        icon.querySelector('i').classList.replace('text-danger', 'text-secondary');
        alert('Property removed from favorites.');
    } else {
        favorites.push(propertyData);
        icon.querySelector('i').classList.replace('bi-heart', 'bi-heart-fill');
        icon.querySelector('i').classList.replace('text-secondary', 'text-danger');
        alert('Property added to favorites.');
    }

    // Update favorites in local storage
    localStorage.setItem('favorites', JSON.stringify(favorites));
}
