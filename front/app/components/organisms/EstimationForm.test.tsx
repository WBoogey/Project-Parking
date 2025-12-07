import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { describe, expect, it, vi } from "vitest";
import EstimationForm from "./EstimationForm";

describe("EstimationForm", () => {
  it("should render all form fields", () => {
    // Arrange
    const handleSubmit = vi.fn();
    render(<EstimationForm onSubmit={handleSubmit} />);

    // Assert
    expect(
      screen.getByText(/Votre place de parking est actuellement/i),
    ).toBeInTheDocument();
    expect(screen.getByText("Libre")).toBeInTheDocument();
    expect(screen.getByText("Déjà louée")).toBeInTheDocument();
    expect(
      screen.getByText("Je me renseigne pour une acquisition"),
    ).toBeInTheDocument();
    expect(screen.getByText("Type de place*")).toBeInTheDocument();
    expect(screen.getByText("Adresse de votre parking*")).toBeInTheDocument();
    expect(screen.getByText("Votre email*")).toBeInTheDocument();
    expect(screen.getByText("Obtenir une estimation")).toBeInTheDocument();
  });

  it("should handle form submission with complete data", async () => {
    // Arrange
    const handleSubmit = vi.fn();
    render(<EstimationForm onSubmit={handleSubmit} />);
    const user = userEvent.setup();

    // Act
    await user.click(screen.getByLabelText("Libre"));
    await user.click(screen.getByText("Type de place"));
    await user.click(screen.getByText("Place de voiture"));
    await user.type(
      screen.getByPlaceholderText(/55 Rue du Faubourg/i),
      "123 Main Street",
    );
    await user.type(
      screen.getByPlaceholderText("Votre email"),
      "test@example.com",
    );
    await user.click(screen.getByText("Obtenir une estimation"));

    // Assert
    expect(handleSubmit).toHaveBeenCalledWith({
      parkingStatus: "free",
      spotType: "Place de voiture",
      address: "123 Main Street",
      email: "test@example.com",
    });
  });

  it("should update radio selection", async () => {
    // Arrange
    const handleSubmit = vi.fn();
    render(<EstimationForm onSubmit={handleSubmit} />);
    const user = userEvent.setup();

    // Act
    await user.click(screen.getByLabelText("Déjà louée"));

    // Assert
    expect(screen.getByLabelText("Déjà louée")).toBeChecked();
  });

  it("should update text inputs", async () => {
    // Arrange
    const handleSubmit = vi.fn();
    render(<EstimationForm onSubmit={handleSubmit} />);
    const user = userEvent.setup();

    // Act
    const addressInput = screen.getByPlaceholderText(/55 Rue du Faubourg/i);
    const emailInput = screen.getByPlaceholderText("Votre email");

    await user.type(addressInput, "Test Address");
    await user.type(emailInput, "email@test.com");

    // Assert
    expect(addressInput).toHaveValue("Test Address");
    expect(emailInput).toHaveValue("email@test.com");
  });

  it.each([
    {
      parkingStatus: "free",
      statusLabel: "Libre",
      spotType: "Place de voiture",
    },
    {
      parkingStatus: "rented",
      statusLabel: "Déjà louée",
      spotType: "Place de moto",
    },
    {
      parkingStatus: "acquisition",
      statusLabel: "Je me renseigne pour une acquisition",
      spotType: "Place de voiture",
    },
  ])(
    "should submit form with $parkingStatus status and $spotType",
    async ({ statusLabel, spotType }) => {
      // Arrange
      const handleSubmit = vi.fn();
      render(<EstimationForm onSubmit={handleSubmit} />);
      const user = userEvent.setup();

      // Act
      await user.click(screen.getByLabelText(statusLabel));
      await user.click(screen.getByText("Type de place"));
      await user.click(screen.getByText(spotType));
      await user.type(
        screen.getByPlaceholderText(/55 Rue du Faubourg/i),
        "Address",
      );
      await user.type(
        screen.getByPlaceholderText("Votre email"),
        "test@test.com",
      );
      await user.click(screen.getByText("Obtenir une estimation"));

      // Assert
      expect(handleSubmit).toHaveBeenCalled();
      expect(handleSubmit.mock.calls[0][0]).toMatchObject({
        spotType,
        address: "Address",
        email: "test@test.com",
      });
    },
  );
});
