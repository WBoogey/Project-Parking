import { render, screen, within } from "@testing-library/react";
import { describe, expect, it, vi } from "vitest";
import PricingCard from "./PricingCard";
import userEvent from "@testing-library/user-event";
import type { PricingCardData } from "@/types/PricingCardTypes";

const mockDataMultipleVehicles: PricingCardData = {
  spots: [
    { variant: "car", price: 150, frequency: "monthly" },
    { variant: "motorcycle", price: 80, frequency: "monthly" },
  ],
  availableSlots: [{ day: "Lundi", hours: ["10:00", "14:00"] }],
};

const mockDataSingleVehicle: PricingCardData = {
  spots: [{ variant: "car", price: 100, frequency: "monthly" }],
  availableSlots: [{ day: "Lundi", hours: ["10:00", "14:00"] }],
};

const mockProps = {
  data: mockDataMultipleVehicles,
  onSubmit: vi.fn(),
};

describe("PricingCard", () => {
  describe("Rendering", () => {
    it.each([
      { text: "Au mois", description: "monthly toggle" },
      { text: "Heure/Jour", description: "hourlyDaily toggle" },
      { text: "J'essaie ce parking", description: "CTA button" },
    ])("should render $description", ({ text }) => {
      // Arrange
      render(<PricingCard {...mockProps} />);

      // Assert
      expect(screen.getByText(text)).toBeInTheDocument();
    });

    it("should display monthly content by default", () => {
      // Arrange
      render(<PricingCard {...mockProps} />);

      // Assert
      expect(screen.getByText("Pour tout véhicule")).toBeInTheDocument();
    });
  });

  describe("Mode switching", () => {
    it.each([
      {
        mode: "monthly" as const,
        clickTarget: "Au mois",
        expectedText: "Pour tout véhicule",
        notExpectedText: "Type de place",
      },
      {
        mode: "hourlyDaily" as const,
        clickTarget: "Heure/Jour",
        expectedText: "Type de place",
        notExpectedText: "Pour tout véhicule",
      },
    ])(
      "should display $mode content when clicking $clickTarget",
      async ({ clickTarget, expectedText, notExpectedText }) => {
        // Arrange
        render(<PricingCard {...mockProps} />);
        const user = userEvent.setup();

        // Act
        await user.click(screen.getByText(clickTarget));

        // Assert
        expect(
          screen.getByText(expectedText, { exact: false }),
        ).toBeInTheDocument();
        expect(screen.queryByText(notExpectedText)).not.toBeInTheDocument();
      },
    );
  });

  describe("Validation - Monthly mode", () => {
    it.each([
      {
        data: mockDataMultipleVehicles,
        shouldBeDisabled: true,
        description: "multiple vehicles and none selected",
      },
      {
        data: mockDataSingleVehicle,
        shouldBeDisabled: false,
        description: "single vehicle (auto-selected)",
      },
    ])(
      "should have CTA $shouldBeDisabled disabled when $description",
      ({ data, shouldBeDisabled }) => {
        // Arrange
        render(<PricingCard data={data} onSubmit={vi.fn()} />);

        // Assert
        const button = screen.getByText("J'essaie ce parking");
        if (shouldBeDisabled) {
          expect(button).toBeDisabled();
        } else {
          expect(button).toBeEnabled();
        }
      },
    );

    it("should enable CTA when vehicle is selected", async () => {
      // Arrange
      render(<PricingCard {...mockProps} />);
      const user = userEvent.setup();

      // Act
      await user.click(screen.getByText("Place de voiture"));

      // Assert
      expect(screen.getByText("J'essaie ce parking")).toBeEnabled();
    });
  });

  describe("Validation - HourlyDaily mode", () => {
    it("should have disabled CTA when no fields are filled", async () => {
      // Arrange
      render(<PricingCard {...mockProps} />);
      const user = userEvent.setup();

      // Act
      await user.click(screen.getByText("Heure/Jour"));

      // Assert
      expect(screen.getByText("J'essaie ce parking")).toBeDisabled();
    });

    it("should have enabled CTA when all fields are filled", async () => {
      // Arrange
      render(<PricingCard {...mockProps} />);
      const user = userEvent.setup();

      // Act
      await user.click(screen.getByText("Heure/Jour"));

      await user.click(screen.getAllByTestId("select-trigger")[0]);
      await user.click(screen.getByTestId("select-choice-Voiture"));

      const startSection = screen.getByTestId("start-reservation");
      const endSection = screen.getByTestId("end-reservation");

      await user.click(
        within(startSection).getAllByTestId("select-trigger")[0],
      );
      await user.click(within(startSection).getByTestId("select-choice-Lundi"));

      await user.click(
        within(startSection).getAllByTestId("select-trigger")[1],
      );
      await user.click(within(startSection).getByTestId("select-choice-10:00"));

      await user.click(within(endSection).getAllByTestId("select-trigger")[0]);
      await user.click(within(endSection).getByTestId("select-choice-Lundi"));

      await user.click(within(endSection).getAllByTestId("select-trigger")[1]);
      await user.click(within(endSection).getByTestId("select-choice-14:00"));

      // Assert
      expect(screen.getByText("J'essaie ce parking")).toBeEnabled();
    });

    it("should have enabled CTA with single vehicle when other fields are filled", async () => {
      // Arrange
      render(<PricingCard data={mockDataSingleVehicle} onSubmit={vi.fn()} />);
      const user = userEvent.setup();

      // Act
      await user.click(screen.getByText("Heure/Jour"));

      const startSection = screen.getByTestId("start-reservation");
      const endSection = screen.getByTestId("end-reservation");

      await user.click(
        within(startSection).getAllByTestId("select-trigger")[0],
      );
      await user.click(within(startSection).getByTestId("select-choice-Lundi"));

      await user.click(
        within(startSection).getAllByTestId("select-trigger")[1],
      );
      await user.click(within(startSection).getByTestId("select-choice-10:00"));

      await user.click(within(endSection).getAllByTestId("select-trigger")[0]);
      await user.click(within(endSection).getByTestId("select-choice-Lundi"));

      await user.click(within(endSection).getAllByTestId("select-trigger")[1]);
      await user.click(within(endSection).getByTestId("select-choice-14:00"));

      // Assert
      expect(screen.getByText("J'essaie ce parking")).toBeEnabled();
    });
  });

  describe("Callback", () => {
    it("should call onSubmit when CTA is clicked and form is valid", async () => {
      // Arrange
      const handleSubmit = vi.fn();
      render(
        <PricingCard data={mockDataSingleVehicle} onSubmit={handleSubmit} />,
      );
      const user = userEvent.setup();

      // Act
      await user.click(screen.getByText("J'essaie ce parking"));

      // Assert
      expect(handleSubmit).toHaveBeenCalledOnce();
    });
  });
});
