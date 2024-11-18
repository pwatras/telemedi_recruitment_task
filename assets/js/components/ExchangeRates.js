import React, { useEffect, useCallback, useMemo } from "react";
import { useHistory, useParams } from "react-router-dom";
import useRates from "../hooks/useRates";
import DatePicker from "./DatePicker";

const ExchangeRatesPage = () => {
  const today = getCurrentDate();

  const { date } = useParams();
  const history = useHistory();
  const [refRates, refRatesLoading] = useRates(today);
  const [currentRates, currentRatesLoading] = useRates(date);

  const onChangeDate = useCallback(
    (value) => {
      history.push(`/exchange-rates/${value}`);
    },
    [history],
  );

  useEffect(() => {
    if (!date) {
      history.push(`/exchange-rates/${today}`);
    }
  }, [today, date]);

  const formatRate = (value) =>
    value ? value.toLocaleString(undefined, { minimumFractionDigits: 4 }) : "-";

  const formatDate = (value) => (value === today ? `Dzisiaj (${date})` : value);

  const needsRefColumns = useMemo(
    () => refRates && currentRates && refRates?.date != currentRates?.date,
    [refRates, currentRates],
  );
  const refDateStr = useMemo(() => formatDate(refRates.date), [refRates]);
  const isLoading = useMemo(
    () => refRatesLoading || currentRatesLoading,
    [refRatesLoading, currentRatesLoading],
  );
  console.log(currentRates.date)

  return (
    <div className="rates-container">
      <div>
        Wybierz datę{" "}
        <DatePicker date={date} onDateChange={onChangeDate} maxDate={today} />
      </div>
      {isLoading ? (
        "Trwa ładowanie kursów walut..."
      ) : (
        <table>
          <thead>
            <tr>
              <th rowSpan={2}>Waluta</th>
              <th colSpan={needsRefColumns ? 2 : 1}>Skup</th>
              <th colSpan={needsRefColumns ? 2 : 1}>Sprzedaż</th>
            </tr>
            <tr>
              <th>{formatDate(currentRates.date)}</th>
              {needsRefColumns && <th>{refDateStr}</th>}
              <th>{formatDate(currentRates.date)}</th>
              {needsRefColumns && <th>{refDateStr}</th>}
            </tr>
          </thead>
          <tbody>
            {currentRates?.rates?.map((rate, key) => (
              <tr key={key}>
                <td>{rate.code}</td>
                <td>{formatRate(rate.buy)}</td>
                {needsRefColumns && (
                  <td>
                    {formatRate(
                      refRates.rates.find(({ code }) => code === rate.code)
                        ?.buy,
                    )}
                  </td>
                )}
                <td>{formatRate(rate.sell)}</td>
                {needsRefColumns && (
                  <td>
                    {formatRate(
                      refRates.rates.find(({ code }) => code === rate.code)
                        ?.sell,
                    )}
                  </td>
                )}
              </tr>
            ))}
          </tbody>
        </table>
      )}
    </div>
  );
};

const getCurrentDate = () => new Date().toISOString().substr(0, 10);

export default ExchangeRatesPage;
