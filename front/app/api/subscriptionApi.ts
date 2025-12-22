import type {
  SubscriptionPriceResponse,
  SubscriptionType,
  WeeklySlot,
} from "@/types/SubscriptionTypes";
import apiClient from "@/api/client";

const WEEKEND_SLOTS: WeeklySlot[] = [
  { dayOfWeek: 5, startHour: "18:00", endHour: "23:59" },
  { dayOfWeek: 6, startHour: "00:00", endHour: "23:59" },
  { dayOfWeek: 0, startHour: "00:00", endHour: "10:00" },
];

const EVENING_SLOTS: WeeklySlot[] = [
  { dayOfWeek: 0, startHour: "18:00", endHour: "23:59" },
  { dayOfWeek: 0, startHour: "00:00", endHour: "08:00" },
  { dayOfWeek: 1, startHour: "18:00", endHour: "23:59" },
  { dayOfWeek: 1, startHour: "00:00", endHour: "08:00" },
  { dayOfWeek: 2, startHour: "18:00", endHour: "23:59" },
  { dayOfWeek: 2, startHour: "00:00", endHour: "08:00" },
  { dayOfWeek: 3, startHour: "18:00", endHour: "23:59" },
  { dayOfWeek: 3, startHour: "00:00", endHour: "08:00" },
  { dayOfWeek: 4, startHour: "18:00", endHour: "23:59" },
  { dayOfWeek: 4, startHour: "00:00", endHour: "08:00" },
  { dayOfWeek: 5, startHour: "18:00", endHour: "23:59" },
  { dayOfWeek: 5, startHour: "00:00", endHour: "08:00" },
  { dayOfWeek: 6, startHour: "18:00", endHour: "23:59" },
  { dayOfWeek: 6, startHour: "00:00", endHour: "08:00" },
];

const TOTAL_SLOTS: WeeklySlot[] = [
  { dayOfWeek: 0, startHour: "00:00", endHour: "23:59" },
  { dayOfWeek: 1, startHour: "00:00", endHour: "23:59" },
  { dayOfWeek: 2, startHour: "00:00", endHour: "23:59" },
  { dayOfWeek: 3, startHour: "00:00", endHour: "23:59" },
  { dayOfWeek: 4, startHour: "00:00", endHour: "23:59" },
  { dayOfWeek: 5, startHour: "00:00", endHour: "23:59" },
  { dayOfWeek: 6, startHour: "00:00", endHour: "23:59" },
];

export const getDefaultSlotsForType = (
  type: SubscriptionType,
): WeeklySlot[] => {
  switch (type) {
    case "total":
      return TOTAL_SLOTS;
    case "weekend":
      return WEEKEND_SLOTS;
    case "evening":
      return EVENING_SLOTS;
    case "custom":
      return [];
  }
};

const BASE_MONTHLY_PRICES: Record<SubscriptionType, number> = {
  total: 150,
  weekend: 80,
  evening: 60,
  custom: 100,
};

export const subscriptionApi = {
  cancelSubscription: async (id: string) => {
    const response = await apiClient.delete(`/subscriptions/${id}`);
    return response.data;
  },

  calculatePrice: async (
    _parkingId: string,
    subscriptionType: SubscriptionType,
    durationMonths: number,
    customSlots?: WeeklySlot[],
  ): Promise<SubscriptionPriceResponse> => {
    let monthlyPrice = BASE_MONTHLY_PRICES[subscriptionType];

    if (
      subscriptionType === "custom" &&
      customSlots &&
      customSlots.length > 0
    ) {
      const hoursPerWeek = customSlots.reduce((total, slot) => {
        const start = parseInt(slot.startHour.split(":")[0], 10);
        const end = parseInt(slot.endHour.split(":")[0], 10);
        return total + (end > start ? end - start : 24 - start + end);
      }, 0);
      monthlyPrice = Math.max(30, Math.round(hoursPerWeek * 2));
    }

    const totalPrice = monthlyPrice * durationMonths;

    return {
      monthlyPrice,
      totalPrice,
      durationMonths,
    };
  },
};
