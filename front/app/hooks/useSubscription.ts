import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { subscriptionApi } from "@/api/subscriptionApi";
import { queryKeys } from "@/api/queryKeyFactory";
import type { CreateSubscriptionData } from "@/types/SubscriptionTypes";

export const useSubscriptions = () => {
  return useQuery({
    queryKey: queryKeys.subscription.list(),
    queryFn: subscriptionApi.getSubscriptions,
  });
};

export const useSubscription = (id: string) => {
  return useQuery({
    queryKey: queryKeys.subscription.detail(id),
    queryFn: () => subscriptionApi.getSubscription(id),
    enabled: !!id,
  });
};

export const useCreateSubscription = () => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: (data: CreateSubscriptionData) =>
      subscriptionApi.createSubscription(data),
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

export const useUpdateSubscription = () => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: ({
      id,
      data,
    }: {
      id: string;
      data: Partial<CreateSubscriptionData>;
    }) => subscriptionApi.updateSubscription(id, data),
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
