export type SubscriptionType = "total" | "weekend" | "evening" | "custom";

export interface WeeklySlot {
  dayOfWeek: number;
  startHour: string;
  endHour: string;
}

export interface SubscriptionDetail {
  id: string;
  userId: string;
  parkingId: string;
  parkingName?: string;
  subscriptionType: SubscriptionType;
  startDate: string;
  endDate: string;
  weeklySlots: WeeklySlot[];
  status: "active" | "cancelled" | "expired";
  monthlyPrice: number;
}

export interface CreateSubscriptionData {
  parkingId: string;
  subscriptionType: SubscriptionType;
  startDate: string;
  durationMonths: number;
  weeklySlots?: WeeklySlot[];
}

export interface SubscriptionPriceResponse {
  monthlyPrice: number;
  totalPrice: number;
  durationMonths: number;
}

export interface StripeCheckoutResponse {
  id: string;
  userId: string;
  parkingId: string;
  rateId: string;
  startDate: string;
  endDate: string;
  weeklySlots: WeeklySlot[];
  checkoutUrl?: string;
  stripeSessionId?: string;
  paymentStatus?: "pending" | "completed" | "failed";
}

export interface CreateCheckoutSessionData {
  parkingId: string;
  subscriptionType: SubscriptionType;
  startDate: string;
  durationMonths: number;
  weeklySlots: WeeklySlot[];
  rateId?: string;
}
