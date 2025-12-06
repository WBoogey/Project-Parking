import { render, screen } from "@testing-library/react";
import { describe, expect, it } from "vitest";
import Navbar from "./Navbar";
import { BrowserRouter } from "react-router";

describe("Navbar", () => {
  it("should render navbar", () => {
    // Arrange
    render(
      <BrowserRouter>
        <Navbar />
      </BrowserRouter>,
    );

    // Assert
    expect(screen.getByRole("navigation")).toBeInTheDocument();
    expect(screen.getAllByRole("button")).toHaveLength(4);
  });
});
