import type { SpotVariant, FrequencyType } from "./SpotsTypes";

export type PricingMode = "monthly" | "hourlyDaily";

export interface SpotPricing {
  variant: SpotVariant;
  price: number;
  frequency: FrequencyType;
}

export interface AvailableSlot {
  day: string;
  hours: string[];
}

export interface PricingCardData {
  spots: SpotPricing[];
  availableSlots: AvailableSlot[];
}
