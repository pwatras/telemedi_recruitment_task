import React, {useCallback} from "react";

const DatePicker = ({ onDateChange, date, maxDate }) => {
    const handleChange = useCallback(
        ({ target }) => {
            onDateChange(target.value);
        },
        [onDateChange],
    );

    return (
        <div>
            <input
                min="2023-01-01"
                max={maxDate}
                type="date"
                value={date ?? ""}
                onChange={handleChange}
            />
        </div>
    );
};
export default DatePicker
