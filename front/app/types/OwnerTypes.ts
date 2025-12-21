export type RateType =
  | "hourly"
  | "daily"
  | "weekly_subscription"
  | "monthly_subscription"
  | "yearly_subscription";

export interface Rate {
  id: string;
  parkingId: string;
  type: RateType;
  calculationRule: string;
  price: number;
  hourlyDiscount: number | null;
  duration: string | null;
}

export interface CreateRateData {
  type: RateType;
  calculationRule: string;
  price: number;
  hourlyDiscount?: number;
  duration?: string;
}
