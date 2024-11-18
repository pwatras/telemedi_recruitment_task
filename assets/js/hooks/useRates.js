import { useEffect, useState } from "react";
const useRates = (date) => {
  const [isLoading, setIsLoading] = useState(false);
  const [rates, setRates] = useState({ date, rates: undefined });
  const getRates = async (date) => {
    const res = await fetch(`/api/rates/${date}`);
    return res.ok ? await res.json() : null;
  };

  useEffect(async () => {
    if (!date) {
      return { rates: undefined, date };
    }
    setIsLoading(true);
    const response = await getRates(date);
    setRates(response);
    setIsLoading(false);
  }, [date, setRates]);

  return [rates, isLoading];
};

export default useRates;
