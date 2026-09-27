/**
 * Formats a number (or numeric string -- Eloquent's decimal casts
 * serialize as strings, e.g. "1999.00") as a BDT price, e.g. ৳1,999.
 * Always whole-taka: paisa isn't used anywhere in this catalog.
 */
export function formatPrice(amount) {
    const value = Number(amount) || 0;

    return '৳' + Math.round(value).toLocaleString('en-US');
}
