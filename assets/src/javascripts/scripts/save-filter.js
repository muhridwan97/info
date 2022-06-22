$(function () {

    const dataFilterKey = 'data-filters';
    const dataFilters = JSON.parse(localStorage.getItem(dataFilterKey) || '[]');

    let pathFound = false;
    for (let data of dataFilters) {
        if (data.hasOwnProperty('pathname')) {
            if (data.pathname === window.location.pathname) {
                pathFound = true;
                data.href = window.location.href;
                break;
            }
        }
    }

    if (!pathFound) {
        dataFilters.push({
            pathname: window.location.pathname,
            href: window.location.href,
            origin: window.location.origin
        })
    }

    dataFilters.forEach(data => {
        const searchSelector = 'a[href="' + data.origin + data.pathname + '"]';
        $('nav#sidebar').find(searchSelector).attr('href', data.href);
    });

    localStorage.setItem(dataFilterKey, JSON.stringify(dataFilters));

});