
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";

document.addEventListener('DOMContentLoaded', () => {

    // Check-in Picker
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');

    if (checkInInput && checkOutInput) {

        const fpCheckIn = flatpickr(checkInInput, {
            dateFormat: "Y-m-d",
            minDate: "today",
            showMonths: 2,
            animate: true,
            disableMobile: "true", // Force custom picker on mobile
            onChange: function (selectedDates, dateStr, instance) {
                // Auto-open check-out
                if (selectedDates.length > 0) {
                    fpCheckOut.set('minDate', dateStr);
                    setTimeout(() => fpCheckOut.open(), 100);
                }
            }
        });

        const fpCheckOut = flatpickr(checkOutInput, {
            dateFormat: "Y-m-d",
            minDate: "today",
            showMonths: 2,
            animate: true,
            disableMobile: "true"
        });
    }
});
