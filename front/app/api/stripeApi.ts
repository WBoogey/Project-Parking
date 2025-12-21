import apiClient from "@/api/client";
import type {
  CreateCheckoutSessionData,
  StripeCheckoutResponse,
} from "@/types/SubscriptionTypes";
import { getDefaultSlotsForType } from "@/api/subscriptionApi";

export const stripeApi = {
  createCheckoutSession: async (
    data: CreateCheckoutSessionData,
  ): Promise<StripeCheckoutResponse> => {
    const weeklySlots =
      data.weeklySlots.length > 0
        ? data.weeklySlots
        : getDefaultSlotsForType(data.subscriptionType);

    const endDate = new Date(data.startDate);
    endDate.setMonth(endDate.getMonth() + data.durationMonths);

    const payload = {
      parkingId: data.parkingId,
      startDate: data.startDate,
      endDate: endDate.toISOString().split("T")[0],
      weeklySlots,
      rateId: data.rateId,
    };

    const response = await apiClient.post<{ data: StripeCheckoutResponse }>(
      "/subscriptions",
      payload,
    );

    return response.data.data;
  },
};
