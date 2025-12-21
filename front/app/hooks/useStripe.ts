import { useMutation, useQueryClient } from "@tanstack/react-query";
import { stripeApi } from "@/api/stripeApi";
import { queryKeys } from "@/api/queryKeyFactory";
import type { CreateCheckoutSessionData } from "@/types/SubscriptionTypes";

export const useCreateCheckoutSession = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (data: CreateCheckoutSessionData) =>
      stripeApi.createCheckoutSession(data),
    onSuccess: (result) => {
      queryClient.invalidateQueries({
        queryKey: queryKeys.subscription.list(),
      });
      queryClient.invalidateQueries({
        queryKey: queryKeys.customer.subscriptions(),
      });

      if (result.checkoutUrl) {
        window.location.href = result.checkoutUrl;
      }
    },
  });
};

export const useStripeCheckout = () => {
  const mutation = useCreateCheckoutSession();

  const initiateCheckout = async (data: CreateCheckoutSessionData) => {
    const result = await mutation.mutateAsync(data);
    return result;
  };

  return {
    initiateCheckout,
    isLoading: mutation.isPending,
    isError: mutation.isError,
    error: mutation.error,
  };
};
