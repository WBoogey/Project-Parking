import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { subscriptionApi } from "@/api/subscriptionApi";
import { queryKeys } from "@/api/queryKeyFactory";
import type { SubscriptionType, WeeklySlot } from "@/types/SubscriptionTypes";

export const useCancelSubscription = () => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: subscriptionApi.cancelSubscription,
    onSuccess: () => {
      queryClient.invalidateQueries({
        queryKey: queryKeys.subscription.list(),
      });
      queryClient.invalidateQueries({
        queryKey: queryKeys.customer.subscriptions(),
      });
    },
  });
};

export const useSubscriptionPrice = (
  parkingId: string,
  subscriptionType: SubscriptionType,
  durationMonths: number,
  customSlots?: WeeklySlot[],
) => {
  return useQuery({
    queryKey: [
      "subscription",
      "price",
      parkingId,
      subscriptionType,
      durationMonths,
      customSlots,
    ],
    queryFn: () =>
      subscriptionApi.calculatePrice(
        parkingId,
        subscriptionType,
        durationMonths,
        customSlots,
      ),
    enabled: !!parkingId && !!subscriptionType && durationMonths > 0,
  });
};
